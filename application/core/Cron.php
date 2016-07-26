<?php
header('Content-Type: text/plain');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// CONST APPLICATION_ENV = "development";

require __DIR__ . '/../../vendor/autoload.php';

ob_start(); // prevent output

// clean php sessions
$mysqli = DatabaseFactory::getFactory()->getMySqli();
$sssn = new Zebra_Session($mysqli, Config::get('HMAC_SALT'));
$sssn->get_active_sessions(); // internally runs gc(), we don't care about the result
unset($sssn);

// Clean old sessions
Session::clean();

// keep subscription active states up to date
SubscriptionModel::validateSubscriptions();
// keep track of trial users
UserModel::trialUserExpire();

ob_clean();
?>