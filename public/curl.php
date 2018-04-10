<!doctype html>
<html><head><style>*{font-family:sans-serif}figure>img{object-fit:scale-down;width:150px;}</style></head><body>
<?php

	/*
	 *
	 *	some setup data which we have been supplied
	 *
	 */

	$host = "https://www.coursesuite.ninja";
	$username = "ea833b2359f5eeea4b795f3633fa49a7";
	$password = "59f2930f0ebbd";


	//$host = "https://coursesuite.ninja.dev";
	//$username = "08ff71edaab9952f06d5f1da1c4acedf";
	//$password = "59fae07eedc9b";




	/*
	 *
	 *	We want to see which apps we can launch
	 *
	 */

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $host . "/api/info/");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization" => "Bearer: $username"));
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 2 in production of course
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10); // longer if you're on a slow link
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




	/*
	 *
	 *	Now, want a token so we can launch apps
	 *
	 */

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $host . "/api/createToken/");
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
	curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
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


	/*
	 *
	 *	Lets draw the links
	 *
	 */

	foreach ($info_model as $index => $app) {

		// token may have characters such as / and $ and . in it - so escape it
		$url = str_replace('{token}', $token_model->token, $app->launch);

		echo "<fieldset><legend>" . $app->name . "</legend>";
		echo "<figure><img src='" . $app->icon . "' />";
		echo "<figcaption>";
		echo "<a href='" . $url . "' target='" . $app->app_key . "'>Launch</a>";
		echo "</figcaption>";
		echo "</fieldset>" . PHP_EOL;

	}


?></body></html>