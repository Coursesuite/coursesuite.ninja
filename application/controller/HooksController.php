<?php

class HooksController//  extends Controller

{

    public function cloudconvert($event = "")
    {
        $raw = file_get_contents('php://input');
        LoggingModel::logInternal("HooksController::cloudconvert", $raw, $event);
        $postbody = json_decode($raw, true);
        if (isset($postbody["step"])) { // ensure we only record completions
            $taken = (int) $postbody["endtime"] - (int) $postbody["starttime"]; // actual time taken
            $minutes = (int) $postbody["minutes"]; // conversion minutes used
            $filename = "";
            $extension = "";
            $size = 0;
            if (isset($postbody["output"])) {
                $size = (int) $postbody["output"]["size"];
            }
            if (isset($postbody["input"])) {
                $filename = $postbody["input"]["filename"];
                $extension = $postbody["input"]["ext"];
            }
            $database = DatabaseFactory::getFactory()->getConnection();
            $query = $database->prepare("insert into conversion_stats (timetaken,minutes,filename,extension,size) values (:taken, :minutes, :name, :ext, :size)");
            $query->execute(array(
                ":taken" => $taken,
                ":minutes" => $minutes,
                ":name" => $filename,
                ":ext" => $extension,
                ":size" => $size,
            ));
        }
    }

}
