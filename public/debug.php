<?php
// header("Content-Type: text/plain");

$url = "https://www2.coursesuite.ninja/api/subscription/activated";
// $url = "https://coursesuite.ninja.dev/api/subscription/activated";
$post_data = array(
	"subscriptionEndDate"=>"",
	"testmode"=>"true",
	"referenceId"=>"COU170228-3333-38124S",
	"productName"=>"docninja-pro",
	"referrer"=>"OTAzYzdiNGNkYjc2ODg2ZDc3YmI2Y2NkMWY2OTQ5MGM0MDI2ZjE1ZjFlNzQ1NGVhOTYzMzk0NWNiZTc4Nzc0M-WK2A1DeKZkMCVLW-K8fXxsSSjqefyDEeZAdK48ZtPr",
	"status"=>"active",
	"statusReason"=>"",
	"subscriptionUrl"=>"https://sites.fastspring.com/coursesuite/order/s/COU170228-3333-20125S",
	"email"=>"julie.e.aldridge%40gmail.com",
	"accountUrl"=>"",
	"security_data"=>"1488243331084COU170228-3333-38124S",
	"security_hash"=>"1511a3794cad74b749cd1c4d93c737fe",
);
$username = "fastspring";
$password = "e93NcNdpntFq";
$options = array(
	CURLOPT_URL => $url,
	CURLOPT_HEADER => false,
	CURLOPT_VERBOSE => false,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => false,
	CURLOPT_SSL_VERIFYPEER => true,
	CURLOPT_USERPWD => $username . ":" . $password,
	CURLOPT_HTTPAUTH => CURLAUTH_DIGEST,
	CURLOPT_POST => true,
	CURLOPT_POSTFIELDS => http_build_query($post_data),
);
// print_r($options);

$ch = curl_init();
curl_setopt_array($ch, $options);

try {
    $raw_response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch), 500);
    }

    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($status_code != 200) {
        throw new Exception("Response with Status Code [" . $status_code . "].", 500);
    }

} catch (Exception $ex) {
    if ($ch != null) {
        curl_close($ch);
    }

    throw new Exception($ex);
}

print_r($raw_response);