<?php

class HooksController //  extends Controller
{

    public function cloudconvert() {
    	$postbody = json_decode(file_get_contents('php://input'));

    	if (isset($postbody->id)) {

    		// todo: put this into a model

    		$taken = (int) $postbody->endtime - (int) $postbody->starttime;
    		$filename = $postbody->output->filename;
    		$extension = $postbody->output->ext;
    		$size = (int) $postbody->output->size;

    		$database = DatabaseFactory::getFactory()->getConnection();
    		$query = $database->prepare("insert into conversion_stats (timetaken,filename,extension,size) values (:taken, :name, :ext, :size)");
    		$query->execute(array(
    			":taken" =>$timetaken,
    			":name"=>$filename,
    			":ext"=>$extension,
    			":size"=>$size,
    		));
    	}

    }

}
