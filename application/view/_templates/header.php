<?php
$start = microtime(true);
$meta_description = isset($this->meta_description) ? $this->meta_description : KeyStore::find("DEFAULT_META_DESCRIPTION")->get(Config::get('DEFAULT_META_DESCRIPTION'));
$meta_keywords = isset($this->meta_keywords) ? $this->meta_keywords : KeyStore::find("DEFAULT_META_KEYWORDS")->get(Config::get('DEFAULT_META_KEYWORDS'));
$meta_title = isset($this->meta_title) ? $this->meta_title : KeyStore::find("DEFAULT_META_TITLE")->get(Config::get('DEFAULT_META_TITLE'));

$baseurl = Config::get('URL');

$is_mobile_browser = ($this->MobileDetect->isMobile() && !$this->MobileDetect->isTablet());
$is_tablet_browser = $this->MobileDetect->isTablet();
$body_id = trim(substr(str_replace("/", "_", $_SERVER['REQUEST_URI']),1));
if (empty($body_id)) $body_id = "default";
?><!doctype html><html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo $meta_title; ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="<?php echo $meta_description; ?>">
	<meta name="keywords" content="<?php echo $meta_keywords; ?>">
	<meta name="author" content="Tim St.Clair <tim.stclair@gmail.com>">

	<link rel="shortcut icon" href="/favicon.ico">
	<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
<?php
	if (isset($this->sheets)) {
		foreach ($this->sheets as $sheet) {
			echo "    <link rel='stylesheet' type='text/css' href='$sheet' />". PHP_EOL;
		}
	}
	if (Config::get("debug") === false) {
		echo "    <link rel='stylesheet' href='" . APP_CSS . "'>" . PHP_EOL;
	} else {
		echo "<link rel='stylesheet/less' type='text/css' href='/css/coursesuite.less' />" . PHP_EOL;
		echo "<script>less = { env: 'development', dumpLineNumbers: 'comments', poll: 99999999 }</script>" . PHP_EOL;
		echo "<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/less.js/3.8.0/less.min.js'></script>" . PHP_EOL;
		echo "<script>less.watch()</script>" . PHP_EOL;
	}
	echo "<style>", Config::CustomCss(true), "</style>", PHP_EOL; // after other styles
	echo KeyStore::find("fastspring_sbl")->get(), PHP_EOL;
	echo KeyStore::find("head_javascript")->get(), PHP_EOL;
?>
	</head>
<body id="<?php echo $body_id; ?>" class="<?php echo ($is_mobile_browser) ? 'mobile' : ($is_tablet_browser) ? 'tablet' : 'desktop'; ?>">
<header class="uk-section uk-cover-container uk-padding-remove-vertical" style="min-height:250px">
    <video src="/img/header.mp4" poster="/img/poster.jpg" autoplay loop muted playsinline uk-cover></video>
	<div class="uk-position-center">
		<a href="https://www.coursesuite.com/"><img src="/img/coursesuite-glyph-logo.svg" width="200"></a>
	</div>
</header>
<?php if (Session::userIsLoggedIn()) { ?>
<section class="uk-section uk-padding-small uk-background-muted">
	<div class="uk-container">
		<p class="uk-text-center">
			You are logged in as <?php echo Session::CurrentUsername(); ?>. <a href="/login/logout">Log Out</a>.
		</p>
	</div>
</section>
<?php } ?>
<main>