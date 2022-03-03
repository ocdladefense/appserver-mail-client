<?php

use Http\Http;
use Http\HttpRequest;
use Http\HttpHeaderCollection;

use function Session\get_current_user;

class MailModule extends Module {

    public function __construct() {

        parent::__construct();
    }



	public function compose() {

		$user = get_current_user();
		if(!$user->isAdmin()) throw new \Exception("You don't have access.");


		$today = new DateTime();
		$pickerDate = $today->format("Y-m-d");
		$emailDate = $today->format("M d, Y");

		$form = new Template("email-form");
		$form->addPath(__DIR__ . "/templates");

		$params = [
			"defaultFrom" => get_current_user()->getEmail()
		];

		return $form->render($params);
	}




	public function getCustomMailFields($mailExtension = "car") {

		if($mailExtension == "standard") return "";

		$moduleName = explode("-", $mailExtension)[0];

		$form = new Template("email-custom-fields");
		$form->addPath(path_to_modules() . "/$moduleName/templates");

		return $form->render();
	}


	public function sendMail() {

		$user = get_current_user();
		if(!$user->isAdmin()) throw new \Exception("You don't have access.");

		$params = $this->getRequest()->getBody();

		if(!empty($params->showPreview)) return $this->getPreview($params);

		var_dump($params);exit;

		$content = $params->body;

		$to = "jbernal.web.dev@gmail.com";// + trevro
		$subject = "Books Online notifications";

		return $this->doMail($to, $subject, $subject, $content);
	}



	public function getPreview($emailType){

		if($emailType == "standard") return "";

		$emailTypeParts = explode("-", $emailType);
		$moduleName = $emailTypeParts[0];
		$messageType = $emailTypeParts[1];

		$messageClass = ucfirst($moduleName) . ucfirst($messageType) . "Message";

		$message = new $messageClass();

		$template = new Template("email");
		$template->addPath(get_theme_path());

		$params = new stdClass();
		$params->court = "Oregon Appellate Court";
		$params->startDate = "2022-01-01";
		$params->endDate = "2022-03-02";

		return $template->render(array(
			"content" => $message->getHtml($params),
			"title" => $message->getTitle()
		));
	}












    // Use composer.json;
    // email: ["className1","className2"] for a list of 
    // classes to instantiate that can implement getTemplates()
    public function showTemplates() {

        return array(
            "templateName",
            "CAR Notifications" => "/car/mail/create",
            "CAR Notifications" => "/mail/car/notifications",
            "BON Notification 1" => "/mail/bon/notification-1",
            "BON Notification 2" => "/mail/bon/notification-2"
        );
    }


	public function doMail($to, $subject, $title, $content, $headers = array()){

		$headers = [
			"From" 		   => "notifications@ocdla.org",
			"Content-Type" => "text/html",
            "Bcc"           => "jbernal.web.dev@gmail.com"
		];

		$headers = HttpHeaderCollection::fromArray($headers);



		$message = new MailMessage($to);
		$message->setSubject($subject);
		$message->setBody($content);
		$message->setHeaders($headers);
		$message->setTitle($title);

		return $message;
	}



	public function testMail() {


		$to = "redderx@yahoo.com";
		$subject = "Books Online notifications";


		$range = new DateTime("2022-1-10");
		$end = new DateTime();
		$content = "My sample content.";
		

		return $this->doMail($to, $subject, "SAMPLE EMAIL", $content);
	}


	public function getScripts() {

		
	}

	public function getStyles() {


	}

}