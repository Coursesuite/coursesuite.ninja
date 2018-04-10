The CourseSuite API lets you generate tokens for *launching* apps, and make modifications to the appearance of the apps. We are planning that a later revision of the API will later be revised with methods for querying the usage patterns of apps, applying custom templates and other functions.

Some of the methods require a BEARER TOKEN header, whilst other methods require DIGEST AUTHENTICATION.

Bearer Tokens are a header attached to the request. For example (curl):

    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization" => "Bearer: $key"));

Digest Authentication is an initial query which sends the username and password in a cipher specified by the server. E.g. (curl)

    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
    curl_setopt($ch, CURLOPT_USERPWD, $key . ":" . $secret);

All methods require HTTPS.

