<?php

class Curl
{
    /**
     * Sets all the options for the mailchimp curl and retuns the result
     * Returns true or false if returnTransfer is false, or returns json data from the request if returnTransfer is true
     *
     * @param url string
     * @param apiKey string
     * @param requestType string
     * @param returnTransfer bool
     * @param json JSONstring
     *
     * @return bool/JSONstring
     */
    public static function mailChimpCurl($url, $apiKey, $requestType, $returnTransfer, $json = null)
    {
        ob_start(); //Just dont look at the errors it'll be fine
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, $returnTransfer);
        if ($json) {curl_setopt($ch, CURLOPT_POSTFIELDS, $json);}
        ob_clean();
        $result = curl_exec($ch);
        curl_close($ch);
        // LoggingModel::logInternal("mailChimpCurl", $url, $apiKey, $requestType, $returnTransfer, $json, $result);
        return $result;
    }

    public static function cloudConvertHook($action, $param = "")
    {
        ob_start();
        $ch = curl_init();
        $server_output = "";
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = array();
        $headers[] = "Authorization: Bearer " . Config::get("CLOUDCONVERT_API_KEY");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        switch ($action) {
            case "subscribe":
                $vars = array(
                    "url" => $param,
                    "event" => "finished",
                );
                curl_setopt($ch, CURLOPT_URL, "https://api.cloudconvert.com/hook");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $vars); //Post Fields
                $server_output = curl_exec($ch);
                break;

            case "unsubscribe":
                $subscription_id = $param;
                curl_setopt($ch, CURLOPT_URL, "https://api.cloudconvert.com/hook/" . $subscription_id);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                $server_output = curl_exec($ch);
                break;

            case "list":
                curl_setopt($ch, CURLOPT_URL, "https://api.cloudconvert.com/hooks");
                $server_output = curl_exec($ch);

        }
        curl_close($ch);
        LoggingModel::logInternal("cloudConvertHook", $action, $param, $server_output);
        return $server_output;
    }
}
