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


		
		$t["default"] = "Choose a template";

		foreach($this->getTemplates() as $key => $name) {
			$t[$key] = $name;
		}

		$today = new DateTime();
		$pickerDate = $today->format("Y-m-d");


		$form = new Template("compose");
		$form->addPath(__DIR__ . "/templates");

		$params = [
			"defaultFrom" => $user->getEmail(),
			"templates" => $t
		];

		return $form->render($params);
	}




	public function getFields($template) {
	
		if($template == "default") return "";

		$tmp = explode("-", $template);
		$module = array_shift($tmp);
		$template = implode("-", $tmp);

		

		$class = $this->loadMailClass($module);

		$content = $class->getCustomFields($template);

		return $content;
	}


	



	public function previewMail($template) {
		// $preview = "<h2>Hello World!</h2>";


		if($template == "default") return "";

		$tmp = explode("-", $template);
		$module = array_shift($tmp);
		$template = implode("-", $tmp);

		

		$class = $this->loadMailClass($module);

		list($subject, $title, $content) = $class->getPreview($template);


		$template = new Template("email");
		$template->addPath(get_theme_path());
		return $template->render(array(
			"content" => $content,
			"title" => $title
		));
	}










	public function sendMail() {

		$user = current_user();
		if(!$user->isAdmin()) throw new \Exception("You don't have access.");


		$to = "jbernal.web.dev@gmail.com";
		$subject = "CAR notifications";

		if($template == "default") return "";

		$tmp = explode("-", $template);
		$module = array_shift($tmp);
		$template = implode("-", $tmp);

	
		$class = $this->loadMailClass($module);

		list($subject, $title, $content) = $class->getPreview($template);

		return $this->createMailMessage($to, $subject, $title, $content);
	}






	public function testMail($template) {

		$user = current_user();
		$to = $user->getEmail();

		if($template == "default") return "";

		$tmp = explode("-", $template);
		$module = array_shift($tmp);
		$template = implode("-", $tmp);

	
		$class = $this->loadMailClass($module);

		list($subject, $title, $content) = $class->getPreview($template);

		return $this->createMailMessage($to, $subject, $title, $content);
	}



	public function createMailMessage($to, $subject, $title, $content, $headers = array()){

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








	private function loadMailClass($name) {

		
		// $module = $this->getModule($name);
		self::loadObject($name);
	
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





}