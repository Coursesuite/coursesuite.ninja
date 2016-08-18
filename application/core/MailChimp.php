<?php

/*
 * Class to handle everything related to mail chimp
 */

class MailChimp
{

    /**
     * Subscribes the user to the mailchimp newsletter
     * PUT request
     *
     * @param $user_email string
     * @param $user_name string
     *
     * @return bool
     */

    public static function subscribe($user_email, $user_name)
    {
        $apiKey = Config::get('MAILCHIMP_API_KEY');
        $memberId = md5($user_email);
        $dataCenter = substr($apiKey, strpos($apiKey, '-') + 1);
        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . config::get('MAILCHIMP_LIST_ID') . '/members/' . $memberId;

        $json = json_encode([
            'email_address' => $user_email,
            'status' => 'subscribed',
            'merge_fields' => [
                'FNAME' => $user_name,
                'LNAME' => '.',
            ],
        ]);
        return Curl::mailChimpCurl($url, $apiKey, 'PUT', true, $json);
    }

    /**
     * Unsubscribes the user from the mailing list
     * PUT request
     *
     * @param $user_email string
     *
     * @return bool
     */

    public static function unsubscribe($user_email)
    {
        $apiKey = Config::get('MAILCHIMP_API_KEY');
        $memberId = md5($user_email);
        $dataCenter = substr($apiKey, strpos($apiKey, '-') + 1);
        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . config::get('MAILCHIMP_LIST_ID') . '/members/' . $memberId;

        $json = json_encode([
            'status' => 'unsubscribed',
        ]);

        return Curl::mailChimpCurl($url, $apiKey, 'PUT', false, $json);
    }

    /**
     * Checks if specified user is subscribed to the mailing list, returns true if they are
     * GET request
     *
     * @param $user_email string
     *
     * @return bool
     */

    public static function isUserSubscribed($user_email)
    {
        $apiKey = Config::get('MAILCHIMP_API_KEY');
        $dataCenter = substr($apiKey, strpos($apiKey, '-') + 1);
        $memberId = md5($user_email);
        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . config::get('MAILCHIMP_LIST_ID') . '/members/' . $memberId;
        $result = json_decode(Curl::mailChimpCurl($url, $apiKey, "GET", true));
        // Check if user is subbed to the list or not. status is 404 if the user has never subbed or was deleted
        if ($result->status == 'subscribed') {
            return true;
        }
        return false;
    }

    /**
     * Returns the id of the interest categorie
     *
     * @return string
     */

    public static function getInterstsId()
    {
        $apiKey = Config::get('MAILCHIMP_API_KEY');
        $dataCenter = substr($apiKey, strpos($apiKey, '-') + 1);
        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . config::get('MAILCHIMP_LIST_ID') . '/interest-categories';
        $result = json_decode(Curl::mailChimpCurl($url, $apiKey, 'GET', true));
        return $result->categories[0]->id;
    }

    /**
     * Returns a 2d array of the different categories and their ID's for the mailing list
     * GET request
     *
     * @return array
     */

    public static function getListInterests()
    {
        $apiKey = Config::get('MAILCHIMP_API_KEY');
        $dataCenter = substr($apiKey, strpos($apiKey, '-') + 1);
        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . config::get('MAILCHIMP_LIST_ID') . '/interest-categories/' . config::get('MAILCHIMP_INTEREST_ID') . '/interests';
        $result = json_decode(Curl::mailChimpCurl($url, $apiKey, "GET", true));
        $interestNames = array();

        foreach ($result->interests as $interests) {
            array_push($interestNames, array($interests->name, $interests->id));
        }
        return $interestNames;
    }

    /**
     * Same as getListInterests but also tells you which interests catagories the user is subbed to
     *
     * @param user_email string
     *
     * @return array
     */

    public static function getUserInterests($user_email)
    {
        $listInterests = MailChimp::getListInterests();

        $apiKey = Config::get('MAILCHIMP_API_KEY');
        $dataCenter = substr($apiKey, strpos($apiKey, '-') + 1);
        $memberId = md5($user_email);
        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . config::get('MAILCHIMP_LIST_ID') . '/members/' . $memberId;
        $curlResult = json_decode(Curl::mailChimpCurl($url, $apiKey, 'GET', true));

        $result = (isset($curlResult->interests) ? $curlResult->interests : null);
        if ($result == null) {
            return $listInterests;
        } else {
            $final = array();
            foreach ($listInterests as $list) {
                $list[] = $result->$list[1];
                $final[] = $list;
            }
            return $final;
        }
    }

    /**
     * Can be used to update pretty much anything, just need to add it in
     *
     * @param $user_email string
     * @param $user_name string optional
     * @param $user_interests array optional
     * @param $subscribed string optional
     *
     * @return bool
     */
    public static function updateUserInfo($user_email, $user_name = null, $user_interests = null, $subscribed = 'subscribed')
    {
        $apiKey = Config::get('MAILCHIMP_API_KEY');
        $dataCenter = substr($apiKey, strpos($apiKey, '-') + 1);
        $memberId = md5($user_email);
        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . config::get('MAILCHIMP_LIST_ID') . '/members/' . $memberId;

        $json = array();
        $json['status'] = $subscribed; //options are: subscribed, unsubscribed, cleaned, pending
        $json['merge_fields'] = array();
        if ($user_name) {$json['merge_fields']['FNAME'] = $user_name;}
        if ($user_interests) {$json['interests'] = $user_interests;}
        $json = json_encode($json);

        return Curl::mailChimpCurl($url, $apiKey, 'PUT', false, $json);
    }

}
