<?php


class CronController extends Controller
{

    public function index()
    {

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
        unset ($database);

    }
}
