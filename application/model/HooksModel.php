<?php

class HooksModel {
	public static function stats() {
		 $database = DatabaseFactory::getFactory()->getConnection();
		 $query = $database->prepare("select avg(size) size, avg(timetaken) timetaken, group_concat(distinct extension) extension FROM conversion_stats");
		 $query->execute();
		 $data = $query->fetch();
		 return array(
		 	"averagesize" => $data->size,
		 	"averagetime" => $data->timetaken,
		 	"extensions" => explode(",",$data->extension),
		 );
	}
}