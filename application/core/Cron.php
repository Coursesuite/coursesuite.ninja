<?php
header('Content-Type: text/plain');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

require '../../vendor/autoload.php';
ob_start(); // prevent output

// keep subscription active states up to date
SubscriptionModel::validateSubscriptions();

ob_clean();
?>