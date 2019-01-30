<?php

class HooksController//  doesn't extend Controller

        //  copy("php://input", $filename); copy file to disk

{

    public function cloudconvert($event = "")
    {
        $raw = file_get_contents('php://input');
        LoggingModel::logInternal("HooksController::cloudconvert", $raw, $event);
        $postbody = json_decode($raw, true);
        if (isset($postbody["step"])) {
            // ensure we only record completions
            $taken = (int) $postbody["endtime"] - (int) $postbody["starttime"]; // actual time taken
            $minutes = (int) $postbody["minutes"]; // conversion minutes used
            $filename = "";
            $extension = "";
            $size = 0;
            if (isset($postbody["output"])) {
                $size = (int) $postbody["output"]["size"];
            }
            if (isset($postbody["input"])) {
                $filename = $postbody["input"]["filename"];
                $extension = $postbody["input"]["ext"];
            }
            $database = DatabaseFactory::getFactory()->getConnection();
            $query = $database->prepare("insert into conversion_stats (timetaken,minutes,filename,extension,size) values (:taken, :minutes, :name, :ext, :size)");
            $query->execute(array(
                ":taken" => $taken,
                ":minutes" => $minutes,
                ":name" => $filename,
                ":ext" => $extension,
                ":size" => $size,
            ));
        }
    }

    // bit bucket configured to point to this server for a MERGE webhook
    public function bitbucket() {

        $destination = KeyStore::find("git-branch")->get("");
        if (empty($destination)) die();

        $raw = file_get_contents('php://input');
        LoggingModel::logInternal("HooksController::bitbucket", $raw);
        $postbody = json_decode($raw);

        // pullrequest set on merge
        if (isset($postbody->pullrequest) && $postbody->pullrequest->state === "MERGED") {
            if ($postbody->pullrequest->destination->branch->name === $destination) {
                $pull = true;
                /*

                    basically since the merge went to the destination we expected, we can do a git pull

                    $root = realpath(dirname(__FILE__) . '/../../');

                    get a shell script to

                    cd $root
                    git checkout $destination
                    git pull

                    or
                    git clone --depth 1 -b PRODUCTION <repo_url>



                    this would assume that the root url is already initialised and has been cloned from the desired branch

                */
            }


        }
    }

    // hook from fastspring after a purchase, should be considered idempotent
    // also FS might/can package multiple events into a single call
    // http://docs.fastspring.com/integrating-with-fastspring/order-flow/subscription-integration
    public function fastspring($event = "order", $status = "failed") {
        $postbody = file_get_contents("php://input");

        // fastspring secret key hashes content for payload verification
        $signature =  base64_encode( hash_hmac( 'sha256', $postbody , Config::get("FASTSPRING_SECRET_KEY"), true ) );
        // $fsSignature = $_SERVER['HTTP_X_FS_SIGNATURE'];

        // if ($hashofbody !== $fsSignature) die("Bad data");

        // record the entire event
        LoggingModel::logMethodCall(__METHOD__, "", $event, $status, $postbody, $_SERVER, $signature);
        $json = json_decode($postbody);

        if (!isset($json->events)) die("Missing model");

        foreach ($json->events as $event) {
            $data = $event->data;

            // if (!isset($data->account->contact->email)) die("Invalid account");
            // if (!isset($data->reference)) die("Invalid order");

            switch ($event->type) {
                case "order.completed": // purchase came in

                    // TODO: differentiate between a subscription and a standalone product such as a template
                    $item = reset($data->items); // items[0]; assume a subscription is for one product only; may change

                    // create and welcome a new user if required
                    if (!Model::Exists("users","user_id=:e",[":e"=>$data->tags->userId])) {
                        RegistrationModel::register_via_fastspring_hook($data->account->contact);
                        $account = new AccountModel("email", $data->account->contact->email);
                    } else {
                        $account = new AccountModel("user_id", $data->tags->userId);
                    }

                    // find the product that this purchase relates to
                    $product = new ProductBundleModel("product_key", $item->product);

                    // create a subscription record and cache this request in the event history
                    $subscription = new dbRow("subscriptions",["fsOrderId=:id",[":id"=>$data->id]]);
                    if ($subscription->PRIMARY_KEY === 0) { // idempotent
                        $subscription->user_id = $account->get_id();
                        $subscription->product_id = $product->get_id();
                        $subscription->referenceId = $data->reference;
                        $subscription->status = 'active';
                        $subscription->testMode = (Config::get("FASTSPRING_PARAM_APPEND") === '&mode=test');
                        $subscription->active = $item->subscription->active;
                        $subscription->fsNextDue = date('Y-m-d', $item->subscription->nextInSeconds);
                        $subscription->fsState = $item->subscription->state;
                        $subscription->fsSubscriptionId = $item->subscription->id; // for https://api.fastspring.com/subscriptions/{id}
                        $subscription->fsOrderId = $data->id; // for https://api.fastspring.com/orders/{id}
                        $subscription->fsInvoiceUrl = $data->invoiceUrl . '/pdf';
                        $subscription->subscriptionUrl = Text::base64enc(Encryption::encrypt($data->account->url)); // management link
                        $subscription->save();

                        // record a history item
                        $subevent = new dbRow("subscription_event");
                        $subevent->user_id = $account->get_id();
                        $subevent->subscription_id = $subscription->PRIMARY_KEY;
                        $subevent->payload = $event; // just this event
                        $subevent->save();
                    }

                break;
                case "subscription.charge.completed": // a new payment has been made on this account

                    $item = reset($data->items); // items[0]; assume a subscription is for one product only; may change

                    // get existing account
                    $account = new AccountModel("email", $data->account->contact->email);

                    // update existing subscription by reference
                    $subscription = new dbRow("subscriptions", ["fsOrderId=:id", [":id" => $data->id]]);
                    $subscription->fsNextDue = date('Y-m-d', $item->subscription->nextInSeconds);
                    $subscription->active = $item->subscription->active;
                    $subscription->fsState = $item->subscription->state;
                    $subscription->fsInvoiceUrl = $data->invoiceUrl . '/pdf';
                    $subscription->statusReason = '';
                    $subscription->save();

                    // record a history item
                    $subevent = new dbRow("subscription_event");
                    $subevent->user_id = $account->get_id();
                    $subevent->subscription_id = $subscription->PRIMARY_KEY;
                    $subevent->payload = $event; // just this event
                    $subevent->save();

                break;
                case "subscription.canceled": // /delete subscription -> subscription.deactivated -> subscription.canceled
                    // get existing account
                    $account = new AccountModel("email", $data->account->contact->email);

                    // update existing subscription by reference
                    $subscription = new dbRow("subscriptions", ["fsSubscriptionId=:id", [":id" => $data->id]]);
                    $subscription->endDate = date('Y-m-d', $data->deactivationDateInSeconds);
                    $subscription->active = $data->active;
                    $subscription->fsState = $data->state;
                    $subscription->statusReason = 'canceled';
                    $subscription->save();

                    // record a history item
                    $subevent = new dbRow("subscription_event");
                    $subevent->user_id = $account->get_id();
                    $subevent->subscription_id = $subscription->PRIMARY_KEY;
                    $subevent->payload = $event; // just this event
                    $subevent->save();

                    // notify the user in case they miss other notifications
                    // $account->notify(Text::get("FEEDBACK_FASTSPRING_ORDER_CANCELLED", array("id" => $subscription->referenceId)), MESSAGE_LEVEL_HAPPY);

                break;

            }
        }
    }

}