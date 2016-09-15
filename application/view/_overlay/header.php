<?php
$start = microtime(true);
$meta_description = Config::get('DEFAULT_META_DESCRIPTION');
$meta_keywords = Config::get('DEFAULT_META_KEYWORDS');
$meta_title = Config::get('DEFAULT_META_TITLE');

if (isset($this->App->meta_description) && !empty($this->App->meta_description)) { $meta_description = $this->App->meta_description; }
if (isset($this->App->meta_keywords) && !empty($this->App->meta_keywords)) { $meta_keywords = $this->App->meta_keywords; }
if (isset($this->App->meta_title) && !empty($this->App->meta_title)) { $meta_title = $this->App->meta_title; }
?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $meta_description; ?>">
    <meta name="keywords" content="<?php echo $meta_keywords; ?>">
    <meta name="author" content="Avide eLearning">
    <title><?php echo $meta_title; ?></title>
    <link rel="icon" href="data:;base64,=">
    <!-- link href='//fonts.googleapis.com/css?family=Roboto' rel='stylesheet' type='text/css' -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="<?php echo Config::get('URL'); ?>css/material.css" />
    <script defer src="https://code.getmdl.io/1.2.1/material.min.js"></script>
    <link href='//r.coursesuite.ninja/mycoursesuite/style.css' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="<?php echo Config::get('URL'); ?>css/style.css" />
<?php
if (isset($this->sheets)) {
    foreach ($this->sheets as $sheet) {
	    if (strpos($sheet, "//") === false) {
		    echo "    <link rel='stylesheet' type='text/css' href='" . Config::get('URL') . "css/$sheet' />" . PHP_EOL;
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
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', '" . $google_analytics_id . "', 'auto');
      ga('send', 'pageview');

    </script>" . PHP_EOL;
}
?>
    </head>
<body id="<?php echo str_replace("/", "_", $filename); ?>">
    <main>