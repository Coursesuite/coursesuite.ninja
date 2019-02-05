<?php

class IntergrationsController extends Controller
{
	private $_base_model;
	/**
	 * Construct this object by extending the basic Controller class.
	 */
	public function __construct($action_name)
	{
		parent::__construct(false,$action_name);
		$this->_base_model = [
			"api_visible" => Config::get("API_VISIBLE")
		];
	}

	public function index() 
	{
		$model = $this->_base_model;
		$this->View->renderHandlebars("intergrations/index", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
	}
}