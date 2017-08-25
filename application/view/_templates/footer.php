    </main>

    <footer class='footer'>
<?php if (false && ($filename !== "login/index" && Session::get("user_account_type") != 7)) {?>
	<section class="section contact-form">
	    <div class="standard-width">
	        <header>
	            <h3>Contact us</h3>
	            <p>*Note: for technical support, please use the <a href="http://help.coursesuite.ninja/" target="_blank" class="not-a-button">Helpdesk</a> or <a href="http://forum.coursesuite.ninja/" target="_blank" class="not-a-button">Forum</a>.</p>
	        </header>
	        <article>
	            <form method="ajax" action="<?php echo Config::get('URL'); ?>store/contact" id="contact-form">
	                <div class="row-grouping flexible">
	                    <div class="name-fields">
	                        <input type="text" name="your-name" required placeholder="Your name (required)">
	                        <input type="email" name="your-email" required placeholder="Your email (required)">
	                    </div>
	                    <div class="message-fields">
	                        <textarea name="your-message" required placeholder="What do you want to talk about?"></textarea>
	                    </div>
	                </div>
	                <div class="row-grouping flexible text-centre">
	                    <div class="recaptcha-holder"></div>
	                    <div><input type="submit" name="action" value="Send message"></div>
	                </div>
	                <div id="form-feedback" class="contact-feedback"></div>
	            </form>
	        </article>
	    </div>
	</section>
<?php } ?>
	    <div class="standard-width">
		    <div class="footer-columns">
			    <div><?php echo KeyStore::find('footer_col1')->get(); ?></div>
			    <div><?php echo KeyStore::find('footer_col2')->get(); ?></div>
			    <div><?php echo KeyStore::find('footer_col3')->get(); ?></div>
		    </div>
	    </div>
    </footer>

    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?=Config::get('URL')?>js/jquery-1.10.2.min.js"><\/script>')</script>

<?php
$baseurl = Config::get('URL');
if (isset($this->scripts)) {
    foreach ($this->scripts as $script) {
	    if (strpos($script, "//") === false) {
		    echo "    <script src='" . Config::get('URL') . "js/$script'></script>" . PHP_EOL;
		} else {
		    echo "    <script src='$script' type='text/javascript'></script>" . PHP_EOL;
		}
    }
}
    echo "<script src='{$baseurl}js/jquery.mb.YTPlayer/jquery.mb.YTPlayer.min.js'></script>" . PHP_EOL;

    if (Config::get("debug") === true) {
	echo "<script src='${baseurl}js/main.js'></script>" . PHP_EOL;
    } else {
	echo "<script src='" . $baseurl . APP_JS . "'></script>" . PHP_EOL;
    }
    echo "<script src='https://www.google.com/recaptcha/api.js?onload=renderGoogleInvisibleRecaptcha&render=explicit' async defer></script>" . PHP_EOL;
    echo "<script id='dsq-count-scr' src='//coursesuite-ninja.disqus.com/count.js' async></script>";

        $end = microtime(true);
        $timestamp = ($end - $start);
        printf("<!-- Page created in %.5f seconds. -->", $timestamp);
?>

    <div class="conditional-ie"><i class='cs-heart'></i> We know you love Internet Explorer, but it just isn't supported anymore. Please use a modern browser instead.</div>

</body>
</html>