<?php

/*
 * Class to handle everything related to mail chimp
 */

class MailChimp
{
    protected $apiKey;
    protected $baseUrl;
    protected $listId;
    protected $interestId;
    protected $memberId;
    protected $userEmail;

    public function __construct($user_email, $list = null, $interest = null) {
        $this->memberId = md5($user_email);
        $this->userEmail = $user_email;
        if (!is_null($list)) {
            $this->listId = $list;
        } else {
            $this->listId = Config::get('MAILCHIMP_LIST_ID');
        }
        if (!is_null($interest)) {
            $this->interestId = $interest;
        } else {
            $this->interestId = Config::get('MAILCHIMP_INTEREST_ID');
        }
        $this->apiKey = Config::get('MAILCHIMP_API_KEY');
        $dataCenter = substr($this->apiKey, strpos($this->apiKey, '-') + 1);
        $this->baseUrl = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $this->listId;
        return $this;
    }

    protected function call($route, $method = 'GET', $json = null)
    {
        $ch = curl_init($this->baseUrl . $route);
        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $this->apiKey);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (!is_null($json)) {curl_setopt($ch, CURLOPT_POSTFIELDS, $json);}
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function subscribe()
    {
        $data = json_encode(array(
            'email_address' => $this->userEmail,
            'status' => 'subscribed',
        ));
        return $this->call("/members/" . $this->memberId, "PATCH", $json);
    }

    public function unsubscribe()
    {
        $data = json_encode(array(
            'status' => 'unsubscribed',
        ));
        return $this->call("/members/" . $this->memberId, "PATCH", $json);
    }

    public function isSubscribed()
    {
        return ($this->subscriptionStatus() == "subscribed");
    }

    public function subscriptionStatus()
    {
        $member = $this->load_member();
        if (isset($member->status)) {
            return $member->status;
        }
        return "";
    }

    // get the interests for the list, cached for up to 30 minutes
    protected function load_interests()
    {
        $key = KeyStore::find("MCInterests" . $this->listId);
        if ($key->age() < 30) {
            $cache = $key->get();
        } else {
            $cache = $this->call("/interest-categories/" . $this->interestId . "/interests");
            $key->put($cache);
        }
        $interests = json_decode($cache)->interests;
        $result = [];
        foreach ($interests as $interest) {
            $result[] = array(
                "id" => $interest->id,
                "name" => $interest->name,
            );
        }
        unset($interests);
        return $result;
    }

    public function getAllInterests()
    {
        return $this->load_interests();
    }

    // loads a list in the context of a user
    protected function load_member()
    {
        $result = json_decode($this->call("/members/" . $this->memberId));
        if ($result->status == "404" || $result->status == 404 || $result->status == "cleaned") { // deleted, bounced or never been subscribed
            return null;
        }
        return $result;
    }

    // get the (merged) list of a users interests, culling any lists that no longer exist
    public function getInterests()
    {
        $list = $this->load_interests();
        foreach ($list as &$entry) {
            $entry["subscribed"] = false;
        }
        $member = $this->load_member();
        if (isset($member->interests)) {
            foreach ($list as &$entry) {
                $entryId = $entry["id"];
                if (property_exists($member->interests, $entryId)) {
                    $entry["subscribed"] = $member->interests->$entryId;
                }
            }
        }
        return $list;
    }

    // set the list of interests that a user is subscribed to
    // list is in the format [["9143cf3bd1" => true, "789cf3bds1" => true]]
    public function setInterests($list)
    {
        $json = array(
            "email_address" => $this->userEmail,
            "status" => "subscribed",
            "interests" => $list
        );
        if (count($list)===0) {
            $json["status"] = "unsubscribed";
            unset($json["interests"]);
        }
        $result = json_decode($this->call("/members/" . $this->memberId, "PATCH", json_encode($json)));
        if ($result->status == "404" || $result->status == 404) {
            $result = json_decode($this->call("/members/", "POST", json_encode($json)));
        }
        return $result;
    }

}
