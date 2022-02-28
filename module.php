<?php

use Http\Http;
use Http\HttpRequest;
use Http\HttpHeaderCollection;
use function Session\get_current_user;

class MailModule extends Module {

    public function __construct() {

        parent::__construct();
    }


    public function mailHome() {

        $tpl = new Template("email");
        $tpl->addPath("/content/themes/default");

        $home = new Template("home");
        $home->addPath(__DIR__ . "/templates");
        $home = $home->render([
            "options"   =>  $this->getMailTypeOptions()
        ]);


        return $tpl->render([
            "title" => "Send Email",
            "content" => $home
        ]);
    }


    public function showFormForType() {

        $today = new DateTime();
		$pickerDate = $today->format("Y-m-d");
		$emailDate = $today->format("M d, Y");

        $primaryTpl = new Template("email");
        $primaryTpl->addPath("/content/themes/default");

        $option = $this->getRequest()->getBody()->option;
        
        $subTemplate = new Template($option);
        $subTemplate->addPath(__DIR__ . "/templates");

        $params = [
			"defaultEmail"      => get_current_user()->getEmail(),
			"emailDate"	        =>  $emailDate,
			"defaultPickerDate" => $pickerDate
		];

        $subTemplate = $subTemplate->render($params);


        return $primaryTpl->render([
            "content" => $subTemplate
        ]);
    }


    public function newMail() {

		$params = $this->getRequest()->getBody();

        $html = $this->getEmailBody($params->emailType);

        var_dump($html);exit;
		

		return $this->sendMail($params->to, $params->subject, $html);
	}


    public function sendMail($to, $subject, $content, $headers = array()){

		$headers = [
			"From" 		   => "notifications@ocdla.org",
			"Content-Type" => "text/html"
		];

		$headers = HttpHeaderCollection::fromArray($headers);



		$message = new MailMessage($to);
		$message->setSubject($subject);
		$message->setBody($content);
		$message->setHeaders($headers);

		return $message;
	}



    public function getEmailBody($type) {

        switch($type){
            case "appellate":
                return $this->getAppellateEmailBody();
                break;
        }
    }


    public function getAppellateEmailBody(){

        $params = $this->getRequest()->getBody();
        var_dump($params);exit;

		$startDate = new DateTime($params->startDate);
		$endDate = new DateTime($params->endDate);

        $http = Http::newSession();
        $req = new HttpRequest("http://appserver/car/get/recent/external");
        $resp = $http->send($req);

        $cars = json_decode($resp->getBody());

        var_dump($cars);exit;

        
    }



    public function getMailTypeOptions() {

        return [
            ""  => "None Selected",
            "appellate" => "Appellate Review",
            "random"  => "Random Email",
            "other"  => "Some Other Email"
        ];
    }
}