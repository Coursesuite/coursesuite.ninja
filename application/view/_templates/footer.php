        </article>
    </section>

    <footer>
        <p><?php
        $end = microtime(true);
        $timestamp = ($end - $start);
        printf("Page created in %.5f seconds.", $timestamp);
 ?></p>
        <p>&copy; <a href="http://coursesuite.ninja">CourseSuite</a> 2015+</p>
    </footer>

    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
    <script src="<?php echo Config::get('URL'); ?>js/main.js"></script>

</body>
</html>