<?php

class IntergrationsModel 
{
	public static function getModel() {
		$database =  DatabaseFactory::getFactory()->getConnection();
		// return all intergration info from db
		// name, link?, other stuff?
	}
}