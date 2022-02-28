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

		$today = new DateTime();
		$pickerDate = $today->format("Y-m-d");
		$emailDate = $today->format("M d, Y");

		$form = new Template("email-form");
		$form->addPath(__DIR__ . "/templates");

		$params = [
			"defaultEmail"		=> get_current_user()->getEmail(),
			"defaultSubject"	=> "Appellate Review - COA, $emailDate",
			"defaultPickerDate" => $pickerDate
		];

		return $form->render($params);
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


	public function doMail($to, $subject, $content, $headers = array()){

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

		return $message;
	}



	public function testMail() {


		$to = "redderx@yahoo.com";
		$subject = "Books Online notifications";


		$range = new DateTime("2022-1-10");
		$end = new DateTime();
		$content = "My sample content.";
		

		return $this->doMail($to, $subject, $content);
	}



}