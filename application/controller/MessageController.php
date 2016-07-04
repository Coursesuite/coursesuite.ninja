<?php
class MessageController extends Controller {

    public function __construct() {
        parent::__construct();
        
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
	    	$this->View->output(Text::get("INCORRECT_USAGE"));
	    }
	    
	    if (!Session::userIsLoggedIn()) {
	    	$this->View->output(Text::get("INCORRECT_USAGE"));
	    }

    }
    
    public function done($m_id) {
		$message_id = intval($m_id);
		$model = array(
			"updated" => MessageModel::markAsRead($message_id)
		);
		$this->View->renderJSON($model);
    }

}