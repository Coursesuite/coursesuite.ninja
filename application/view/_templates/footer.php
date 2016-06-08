    </main>

    <footer>
        <p class='unimportant'><?php
        $end = microtime(true);
        $timestamp = ($end - $start);
        printf("Page created in %.5f seconds.", $timestamp);
 ?></p>
 		<p>
	 		<a href="#">Support</a>
	 		<a href="#">Privacy</a>
	 		<a href="#">Contact Us</a>
 		</p>
        <p>&copy; <a href="http://coursesuite.ninja">CourseSuite</a> 2016-3016</p>
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