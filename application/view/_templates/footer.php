    </main>

    <footer>
	    <div class="standard-width">
		    <div class="footer-columns">
			    <div><?php echo Config::get('GLOBAL_FOOTER_COLUMN_1'); ?></div>
			    <div><?php echo Config::get('GLOBAL_FOOTER_COLUMN_2'); ?></div>
			    <div><?php echo Config::get('GLOBAL_FOOTER_COLUMN_3'); ?></div>
		    </div>
	    </div>
    </footer>

    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?=Config::get('URL')?>js/jquery-1.10.2.min.js"><\/script>')</script>

<?php
if (isset($this->scripts)) {
    foreach ($this->scripts as $script) {
	    if (strpos($script, "//") === false) {
		    echo "    <script src='" . Config::get('URL') . "js/$script'></script>" . PHP_EOL;
		} else {
		    echo "    <script src='$script' type='text/javascript'></script>" . PHP_EOL;
		}
    }
} ?>
    <script src="<?php echo Config::get('URL'); ?>js/main.js"></script>
	<?php
        $end = microtime(true);
        $timestamp = ($end - $start);
        printf("<!-- Page created in %.5f seconds. -->", $timestamp);
	?>
</body>
</html>