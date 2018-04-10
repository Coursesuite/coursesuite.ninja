## PHP Example (using curl)

```php
<?php

/**
 * enter your apikey and secret as the username and password
 *
 */
$host = "https://www.coursesuite.ninja";
$username = "c055588e18df56f877f3c3ca73790ecd";
$password = "b6e8b4699";

/**
 * call the info endpoint to determine which tools are available
 *
 */
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $host . "/api/info/");
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization" => "Bearer: $username"));
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 2);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$resp = curl_exec($ch);
if (curl_errno($ch)) {
    die(curl_error($ch));
}
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($status_code != 200) {
    die("Error $status_code $resp");
}
curl_close($ch);
$info_model = json_decode($resp);

/**
 * generate a token for launch
 *
 */
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $host . "/api/createToken/");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 2);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$resp = curl_exec($ch);
if (curl_errno($ch)) {
    die(curl_error($ch));
}
$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($status_code != 200) {
    die("Error $status_code $resp");
}
curl_close($ch);
$token_model = json_decode($resp);

/**
 * render a basic document that contains the links to the apps
 *
 */

 ?>
<!doctype html>
<html>
<body>
<?php
foreach ($info_model as $index => $app) {

    $url = str_replace('{token}', $token_model->token, $app->launch);

    echo "<fieldset><legend>" . $app->name . "</legend>";
    echo "<figure><img src='" . $app->icon . "' />";
    echo "<figcaption>";
    echo "<a href='" . $url . "' target='" . $app->app_key . "'>Launch</a>";
    echo "</figcaption>";
    echo "</fieldset>" . PHP_EOL;

}
?>
</body></html>
```

## Getting Support
If you need any help related to the API, please [raise a ticket](https://help.coursesuite.ninja/open.php) on our helpdesk.
