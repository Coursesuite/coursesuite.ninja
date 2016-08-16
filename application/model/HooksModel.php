<?php

class HooksModel
{
    public static function stats()
    {
        $database = DatabaseFactory::getFactory()->getConnection();
        // group_concat(distinct extension) extension
        $query = $database->prepare("select avg(size) size, avg(minutes) minutes, avg(timetaken) timetaken FROM conversion_stats");
        $query->execute();
        $data = $query->fetch();
        $rows = $database->query("select extension, count(extension) `count` FROM conversion_stats group by extension order by count(extension) desc")->fetchAll();
        return array(
            "averagesize" => Text::formatBytes((int) $data->size),
            "averagetime" => Text::formatTime((int) $data->timetaken),
            "averageminutes" => (int) $data->minutes,
            "extensions" => $rows, // explode(",",$data->extension),
        );
    }

}
