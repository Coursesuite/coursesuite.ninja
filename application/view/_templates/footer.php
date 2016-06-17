    </main>

    <footer>
        <p class='unimportant'><?php
        $end = microtime(true);
        $timestamp = ($end - $start);
        printf("Page created in %.5f seconds.", $timestamp);
 ?></p>
 		<p>
	 		<a href="<?php echo Config::get('URL'); ?>content/support">Support</a>
	 		<a href="<?php echo Config::get('URL'); ?>content/privacy">Privacy</a>
	 		<a href="<?php echo Config::get('URL'); ?>content/about">About Us</a>
 		</p>
        <p><?php echo Config::get('DEFAULT_META_COPYRIGHT');?></p>
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

</body>
</html>