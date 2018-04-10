<?php

class ServicesController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index() {
	$model = StoreModel::get_store_index_model();
	$model->csrf_token = Csrf::makeToken();

	$this->View->renderHandlebars("services/index", $model, "_templates", Config::get("FORCE_HANDLEBARS_COMPILATION"));
    }

    public function email() {
		if (!Csrf::isTokenValid()) {
			die("invalid usage");
		}

		$name = Request::post("name", true, FILTER_SANITIZE_STRING);
		$email = Request::post("email", true, FILTER_SANITIZE_EMAIL);
		$website = Request::post("website", true, FILTER_SANITIZE_URL);
		$budget = Request::post("budget", true, FILTER_SANITIZE_STRING);
		$timeframe = Request::post("timeframe", true, FILTER_SANITIZE_STRING);
		$details = Request::post("details", true, FILTER_SANITIZE_STRING);

		$mailer = new Mail;
		$message = [];

		$message[] = "Name: $name" . PHP_EOL;
		$message[] = "Email: $email" . PHP_EOL;
		$message[] = "Website: $website" . PHP_EOL;
		$message[] = "Budget: $budget" . PHP_EOL;
		$message[] = "Timeframe: $timeframe" . PHP_EOL . PHP_EOL;
		$message[] = $details . PHP_EOL;

	    $mail_sent = $mailer->sendMail(Config::get('EMAIL_SUBSCRIPTION'), $email, $name, "CourseSuite Contact Form", "Someone just used the contact form. Here's what they put in:" . PHP_EOL . PHP_EOL . implode('', $message));

	    $model = new stdClass();
	    $model->baseurl = Config::get("URL");
	    $model->mail_sent = $mail_sent;
	    $model->message = implode('', $message);

	    $this->View->renderHandlebars("services/sent", $model, "_templates", Config::get("FORCE_HANDLEBARS_COMPILATION"));
    }
}