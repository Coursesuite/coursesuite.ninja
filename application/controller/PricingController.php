<?php

class PricingController extends Controller {
	function __construct() {
		parent::__construct();
	}

	function index() {

		$cache = CacheFactory::getFactory()->getCache();
	    $cacheItem = $cache->getItem("pricing_model");
	    $model = $cacheItem->get();
	    if (is_null($model)) {

			$model = new stdClass();

			// the order and items I want on my pricing page
			$pricing_order = explode(',', KeyStore::find("pricing_products")->get());

			foreach ($pricing_order as $id) {
				$pb = new dbRow("product_bundle", $id);
				if ($pb->active) {
					$obj = new stdClass();
					$obj->price = $pb->price;
					$obj->fs_key = $pb->product_key;
					$obj->fs_url = $pb->store_url;
					if (strpos($pb->app_ids, ',') === false) { // $pb->label should = "Standalone"
						$app = new dbRow("apps", (int) $pb->app_ids);
						$obj->key = $app->app_key;
						$obj->label = $app->name;
						$obj->tag = $app->tagline;
						$obj->buy = $app->active === 3;
						$obj->home = "/home/{$app->app_key}";
						$obj->icon = 'data:image/svg+xml,' . rawurlencode($app->glyph);
					} else {
						$obj->label = $pb->label;
						$obj->icon = $pb->icon;
						$apps = explode(',',$pb->app_ids);
						$obj->home = "/home/" . AppModel::app_key_for_id($apps[0]); // first app in this bundle
						$obj->tag = count($apps) . " apps, one price.";
						$obj->key = "bundle";
						$obj->buy = false;
					}
					$obj->icon_tag = strpos($obj->icon, "svg")!==false ? " uk-svg" : "";
					$obj->features = Text::toHtml($pb->pricing_description);
					$model->Pricing[] = $obj;
				}
			}
			$model->Heading = Text::toHtml(KeyStore::find("pricing_text")->get());
			$model->ContextualStore = Config::get("FASTSPRING_CONTEXTUAL_STORE");

			$cacheItem->set($model)->expiresAfter(86400)->addTags(["coursesuite","model"]); // 1 day
			$cache->save($cacheItem);

		}

		$this->View->renderHandlebars("pricing/index", $model);
	}
}