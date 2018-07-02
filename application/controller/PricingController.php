<?php

class PricingController extends Controller {
	function __construct() {
		parent::__construct();
	}

	function index() {
		$model = new stdClass();
		// $model->ProductBundles = ProductBundleModel::get_all_models(true);
		$model->ProductBundles = array_filter(ProductBundleModel::get_all_models(true), function($v) {
			if (is_null($v)) return;
			if ($v->active !== "1") return false;
			return true;
		});
		// $this->View->Requires("pricing/tile");
		$this->View->Requires("pricing/item");
		$this->View->renderHandlebars("pricing/index", $model);
	}
}