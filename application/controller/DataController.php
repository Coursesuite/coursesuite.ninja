<?php

class DataController extends Controller {
	function __construct($action_name) {
		parent::__construct(false,$action_name);
		parent::allowCORS();
		parent::requiresAjax();
	}

	public function alert() {
		$message_id = Request::post("message_id");
		MessageModel::markAsRead($message_id);
	}
}