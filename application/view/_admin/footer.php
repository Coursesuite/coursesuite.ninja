<?php $this->renderTemplates(); ?>

<?php

if (isset($this->scripts)) {
	foreach ($this->scripts as $script) {
		echo "<script type='text/javascript' src='$script'></script>" . PHP_EOL;
	}
}

$end = microtime(true);
$timestamp = ($end - $start);
printf("<!-- Page created in %.5f seconds. -->", $timestamp);
?>
</body>
</html>