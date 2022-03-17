<?php

use Http\Http;
use Http\HttpRequest;
use Http\HttpHeaderCollection;



class MailModule extends Module {


	
    public function __construct() {

        parent::__construct();
    }



	public function compose() {

		$user = current_user();


		$t = $this->getTemplates();
		$t["default"] = "Choose a template";

		// var_dump($templates);exit;


		$today = new DateTime();
		$pickerDate = $today->format("Y-m-d");
		$emailDate = $today->format("M d, Y");

		$form = new Template("compose");
		$form->addPath(__DIR__ . "/templates");

		$params = [
			"defaultFrom" => $user->getEmail(),
			"templates" => $t
		];

		return $form->render($params);
	}




	public function getCustomMailFields($template) {
		return "<input placeholder='foobar' />";

		$tmp = explode("-", $template);
		$module = $tmp[0];
		$template = $tmp[1];

		$form = new Template($template);
		$form->addPath(path_to_modules() . "/$module/templates");

		return $form->render();
	}


	



	public function getPreview($template) {
		// $preview = "<h2>Hello World!</h2>";


		if($template == "default") return "";

		$tmp = explode("-", $template);
		$module = array_shift($tmp);
		$template = implode("-", $tmp);

		

		$class = $this->loadMailClass($module);

		return $class->getPreview($template);
	}









	private function loadMailClass($name) {

		
		$module = $this->getModule($name);


		$path = $module["path"] . "/src/Mail.php";
		require $path;
	

	
		$ns = ucwords($name);
		$class = "\\{$ns}\\Mail";
		
		return new $class();
	}




    public function getTemplates() {

		$modules = $this->query("mail",true);

		// Paths to each module's respective Mail class.
		$paths = array();

		// Array of classes to instantiate.
		$classes = array();

		// Array of string templates.
		$templates = array();

		/*
		foreach($modules as $module) {
			$paths []= $module["path"] . "/src/Mail.php";
		}

		foreach($paths as $path) {
			require $path;
		}
		*/

		foreach($modules as $name => $module) {
			$ns = ucwords($name);
			$class = "\\{$ns}\\Mail";
			$classes []= $class;
		}

		foreach($classes as $class) {
			$instance = new $class();
			$tlist = $instance->getTemplates();
			$templates = array_merge($templates, $tlist);
		}


		return $templates;
    }







	public function sendMail() {

		$user = current_user();
		if(!$user->isAdmin()) throw new \Exception("You don't have access.");

		$params = $this->getRequest()->getBody();

		if(!empty($params->showPreview)) return $this->getPreview($params);

		var_dump($params);
		exit;

		$content = $params->body;

		$to = "jbernal.web.dev@gmail.com";// + trevro
		$subject = "Books Online notifications";

		return $this->doMail($to, $subject, $subject, $content);
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