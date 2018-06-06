<?php
$start = microtime(true);
$baseurl = Config::get('URL');
function CurrentMenu($page, $routes, $classnames = '') {
    $classes = [$classnames];
    if (strpos($routes,$page)!==false) $classes[] = "uk-active";
    echo " class='" . implode(' ', $classes) . "'";
}

?><!doctype html><html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>coursesuite admin tool</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Inconsolata" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
<?php
    echo "<style>", AppModel::apps_colours_css(), "</style>", PHP_EOL;
    if (isset($this->sheets)) {
        foreach ($this->sheets as $sheet) {
            echo "    <link rel='stylesheet' type='text/css' href='$sheet' />". PHP_EOL;
        }
    }
    if (Config::get("debug") !== false) {
        echo "<link rel='stylesheet/less' type='text/css' href='/css/admin.less' />" . PHP_EOL;
        echo "<script>less = { env: 'development', dumpLineNumbers: 'comments', poll: 10000 }</script>" . PHP_EOL;
        echo "<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/less.js/2.7.3/less.min.js'></script>" . PHP_EOL;
        echo "<script>less.watch()</script>" . PHP_EOL;
    }
?>
</head>
<body id="coursesuite-admin">
<header>
	<h1><i class="fa fa-plug"></i> Coursesuite Back End</h1>
	<p><a href="<?php echo $baseurl; ?>login/logout">Log Out</a></p>
</header>