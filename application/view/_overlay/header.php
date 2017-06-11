<?php
$start = microtime(true);
$baseurl = Config::get('URL');
$meta_description = Config::get('DEFAULT_META_DESCRIPTION');
$meta_keywords = Config::get('DEFAULT_META_KEYWORDS');
$meta_title = Config::get('DEFAULT_META_TITLE');

if (isset($this->App->meta_description) && !empty($this->App->meta_description)) { $meta_description = $this->App->meta_description; }
if (isset($this->App->meta_keywords) && !empty($this->App->meta_keywords)) { $meta_keywords = $this->App->meta_keywords; }
if (isset($this->App->meta_title) && !empty($this->App->meta_title)) { $meta_title = $this->App->meta_title; }
?><!doctype html>
<html class="no-touch">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $meta_description; ?>">
    <meta name="keywords" content="<?php echo $meta_keywords; ?>">
    <meta name="author" content="Avide eLearning">
    <title><?php echo $meta_title; ?></title>
    <link rel="icon" href="data:;base64,=">
    <link href="//fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
<?php if (Config::get("debug") === false) { ?>
    <link rel="stylesheet" href="<?php echo $baseurl; ?>css/compiled.css">
<?php } else { ?>
    <link rel="stylesheet/less" type="text/css" href="<?php echo $baseurl; ?>css/less/styles.less">
    <script src="//cdnjs.cloudflare.com/ajax/libs/less.js/2.7.2/less.min.js"></script>
    <script>window.less || document.write('<script src="<?=Config::get('URL')?>js/less.min.js"><\/script>')</script>
<?php } ?>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon"><?php
if (isset($this->sheets)) {
    foreach ($this->sheets as $sheet) {
      if (strpos($sheet, "//") === false) {
        echo "    <link rel='stylesheet' type='text/css' href='" . $baseurl . "css/$sheet' />" . PHP_EOL;
      } else {
        echo "    <link rel='stylesheet' type='text/css' href='$sheet' />". PHP_EOL;
    }
    }
}
$google_analytics_id = Config::get('GOOGLE_ANALYTICS_ID');
if (isset($google_analytics_id) && (!empty($google_analytics_id))) {
    echo "<script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

      ga('create', '" . $google_analytics_id . "', 'auto');
      ga('send', 'pageview');

    </script>" . PHP_EOL;
}
?>
    </head>
<body id="<?php echo str_replace("/", "_", $filename); ?>">
    <main>