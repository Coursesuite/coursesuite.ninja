<?php

if (isset($this->Registration) && !empty($this->Registration)) {
	if ($this->MobileDetect->isMobile() && !$this->MobileDetect->isTablet()) {
		include "login_mobile.php";
	} else {
		include "login_desktop.php";
	}
}
?>
</main>

<footer class="uk-section">
	<div class="uk-container">
		<p class="uk-text-center">&copy; Copyright <?php echo "2012 - ", date("Y"); ?> <a href="https://www.coursesuite.com">Coursesuite Ltd</a>. Email: <a href="mailto:hello@coursesuite.com">hello@coursesuite.com</a> (<a href="https://www.coursesuite.com/privacy">Privacy</a>, <a href="https://www.coursesuite.com/terms">Legals</a>) ABN: 72182107810</p>
	</div>
</footer>

<?php $this->renderTemplates(); ?>

<?php

if (isset($this->scripts)) {
	foreach (array_unique($this->scripts) as $script) {
		echo "<script type='text/javascript' src='$script'></script>" . PHP_EOL;
	}
}

$end = microtime(true);
$timestamp = ($end - $start);
printf("<!-- Page created in %.5f seconds -->", $timestamp);
?>
</body>
</html>