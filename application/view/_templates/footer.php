<?php

if (isset($this->Registration) && !empty($this->Registration)) {
	if ($this->MobileDetect->isMobile() && !$this->MobileDetect->isTablet()) {
		include "login_mobile.php";
	} else {
		include "login_desktop.php";
	}
}

if (isset($this->SystemMessages)) {
	$this->scripts[] = "/js/alerts.js";
	echo "<div class=' uk-position-fixed uk-position-top-left cs-alerts uk-position-small uk-width-1-4 uk-animation-slide-top'>";
	$lastrecord = "";
    foreach ($this->SystemMessages as $message) {
        switch (intval($message["level"],10)) {
            case 0: $message_level = "danger"; break;
            case 1: $message_level = "warning"; break;
            case 2: $message_level = "success"; break;
            case 3: $message_level = "primary"; break;
        }
        $message_id = $message["message_id"];
        $record = md5($message_level . $message_id . $message["text"]);
        if ($record !== $lastrecord) {
			echo "<div class='uk-alert-{$message_level} ' uk-alert>";
			if (intval($message["user_id"],10) !== 0) {
 				echo "<a class='uk-alert-close' uk-close onclick='acknowledge({$message_id})'></a>";
             } else {
				echo '<a class="uk-alert-close" uk-close></a>';
			}
			echo Text::toHtml($message["text"]);
			echo "</div>", PHP_EOL;
		};
		$lastrecord = $record;
    }
    echo "</div>";
}
?>
    </main>

	<div class='uk-section cs-footer'>
		<div class='uk-container'>
			<?php
			echo Text::compileHtml(KeyStore::find("page_footer")->get(), array(
				"loggedOn" => Session::userIsLoggedIn(),
				"username" => Session::CurrentUsername()
			));
			?>
		</div>
    </div>

<?php $this->renderTemplates(); ?>

<?php

if ($this->MobileDetect->isMobile() || $this->MobileDetect->isTablet()) {
	include "nav_mobile_offscreen.php";
}

if (isset($this->scripts)) {
	foreach (array_unique($this->scripts) as $script) {
		echo "<script type='text/javascript' src='$script'></script>" . PHP_EOL;
	}
}

$end = microtime(true);
$timestamp = ($end - $start);
printf("<!-- Page created in %.5f seconds -->", $timestamp);
// var_dump([$GLOBALS,$this->Registration]);
?>
</body>
</html>