<?php
header('Content-Type: text/plain');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// CONST APPLICATION_ENV = "tim";

require __DIR__ . '/../../vendor/autoload.php';

ob_start(); // prevent output



$database = DatabaseFactory::getFactory()->getConnection();
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$database->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
try {
    $database->query("
        SET @qs = (
            select group_concat(
                concat(
                    'select id,method from systasks where id = ', i.id, ' and running = 0 and lastrun <= unix_timestamp(', i.frequency, ')'
                )
                separator ' union '
            )
            from (
                select id,frequency from systasks
            ) i
        );
        PREPARE stmt FROM @qs;
    ");
    $results = $database->query("EXECUTE stmt;");
    while (list($id,$method) = $results->fetch(PDO::FETCH_NUM)) {
        if (is_callable($method)) {
        $database->prepare("UPDATE systasks SET running=1 WHERE id=:id")->execute(array(":id"=>$id));
        // list($class,$function) = explode('::',$method);
        // then list paths and see if method exists in file named path . $method . php then require that file
        // echo "<li>$class exists " .  class_exists($class);
        call_user_func($method); // rather than just $method(); this way it invokes the autoloader
        $database->prepare("UPDATE systasks SET lastrun=unix_timestamp(CURRENT_TIMESTAMP), running=0 WHERE id=:id")->execute(array(":id"=>$id));
        }
    }
    $database->query("DEALLOCATE PREPARE stmt; SET @qs = null;");
}
catch (PDOException $e)
{
    echo $e->getMessage();
    die();
}

ob_clean();