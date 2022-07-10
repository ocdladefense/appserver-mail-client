<?php

use Http\Http;
use Http\HttpRequest;
use Http\HttpHeaderCollection;



class MailModule extends Module {


	const DEFAULT_EMAIL = "jbernal.web.dev@gmail.com";

	
    public function __construct() {

        parent::__construct();
    }




	/**
	 * @method compose
	 * 
	 * Show an email composition form to the user.  
	 */
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



	/**
	 * @method getFields
	 * 
	 * Customize the mail form using any additional fields specified by the 
	 * implementing module.  These are normally stored in the
	 * custom-fields template file for each module.
	 */
	public function getFields($template) {
	
		if($template == "default") return "";

		$tmp = explode("-", $template);
		$module = array_shift($tmp);
		$template = implode("-", $tmp);

		

		$class = $this->loadMailClass($module);

		$content = $class->getCustomFields($template);

		return $content;
	}


	
	public function loadMailMessages($module, $template, $params) {

		$class = $this->loadMailClass($module);


		$subjects = method_exists($class, "getSubjects") ? $class->getSubjects($params) : array($class->getSubject($params));

		$bodies = method_exists($class, "getHtmlBodies") ? $class->getHtmlBodies($template, $params) : array($class->getHtmlBody($params));

		$titles = method_exists($class, "getTitles") ? $class->getTitles($params) : array($class->getTitle($params));

		$emails = method_exists($class, "getEmails") ? $class->getEmails($params) : array();



		$list = new \MailMessageList();
		// $list->add($message);

		$generator = function($body, $index) use($emails,$subjects,$titles,$list) {
			$subject = $subjects[$index];
			$title = $title[$index];
			$email = $emails[$index] ?? self::DEFAULT_EMAIL;
			$message = self::createMailMessage($emails, $subject, $title, $body);
			$list->add($message);
		};

		array_walk($bodies, $generator);

		return $list;
	}


	public function previewMail($tpl) {

		$req = $this->getRequest();
		$params = $req->getBody();


		$user = current_user();
		// $emails = $user->getEmail() ?? "jbernal.web.dev@gmail.com";

		if($template == "default") return "";

		list($module,$template) = $this->parseTemplate($tpl);
		

		$list = $this->loadMailMessages($module,$template,$params);

		// var_dump($list);
		return $list->getFirst()->getBody();
		// return $messages[0];
	}



	





	public function testMail($tpl) {

		$req = $this->getRequest();
		$params = $req->getBody();


		$user = current_user();
		$email = $user->getEmail() ?? "jbernal.web.dev@gmail.com";

		if($template == "default") return "";

		list($module,$template) = $this->parseTemplate($tpl);
		

		$list = $this->loadMailMessages($module,$template,$params);

		/*
		        if(get_class($message) == "MailMessage") {
            $list = new MailMessageList();
            $list->add($message);
        } else {
            $list = $message;
        }
		*/

		foreach($list as $message) {
			$message->setTo($email);
		}

		$results = MailClient::sendMail($list);
		
		return $results;
	}





	public function sendMail($tpl) {

		$user = current_user();
		$cc = $user->getEmail();

		if($tpl == "default") return "";
		
		list($module,$template) = $this->parseTemplate($tpl);

		$list = $this->loadMailMessages($module,$template,$params);

		foreach($list as $message) {
			$message->setTo($emails);
		}

		$results = false; // MailClient::sendMail($list);
		
		return $results;
	}















	public function createMailMessage($to, $subject, $title, $content, $headers = array()){
		$template = new Template("email");
		$template->addPath(get_theme_path());
		$body = $template->render(array(
			"content" 	=> $content,
			"title" 	=> $title
		));



		$headers = [
			"From" 		   	=> "notifications@ocdla.org",
			"Content-Type" 	=> "text/html",
            "Bcc"           => "jbernal.web.dev@gmail.com"
		];

		$headers = HttpHeaderCollection::fromArray($headers);



		$message = new MailMessage($to);
		$message->setSubject($subject);
		$message->setBody($body);
		$message->setHeaders($headers);
		$message->setTitle($title);

		return $message;
	}





	/**
	 * Get MailMessage objects by delegating processing
	 * to the underlying Mail implementation.
	 * 
	 * A mail template identifier should be a dash-separated string,
	 * prefixed with the name of the module that provides the template.  For example, 
	 * bon
	 */
	public function parseTemplate($template) {

		$req = $this->getRequest();
		$body = $req->getBody();


		$tmp = explode("-", $template);
		$module = array_shift($tmp);
		$template = implode("-", $tmp);

	
		return array($module,$template);
	}



	private function loadMailClass($name) {

		
		// $module = $this->getModule($name);
		self::loadInstance($name);
	
		$ns = ucwords($name);
		$class = "\\{$ns}\\Mail";
		
		return new $class();
	}


	




	/**
	 * @method getTemplates
	 * 
	 * Build a list of all modules implementing mail functionality.
	 * Also build a list of all of their respective templates for display in the drop-down.
	 */
    public function getTemplates() {

		$modules = $this->query("mail",true);

		// Paths to each module's respective Mail class.
		$paths = array();

		// Array of classes to instantiate.
		$classes = array();

		// Array of string templates.
		$templates = array();


		foreach($modules as $name => $module) {
			$ns = ucwords($name);
			$class = "\\{$ns}\\Mail";
			$classes[$name]= $class;
		}

		foreach($classes as $module => $class) {
			$instance = new $class();
			$tlist = $instance->getTemplates();

			foreach($tlist as $tpl => $meta) {
				$fqn = $module . "-" .$tpl;
				$templates[$fqn] = $meta["name"];
			}

			
		}


		return $templates;
    }





}