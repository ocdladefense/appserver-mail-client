<?php

use Http\Http;
use Http\HttpRequest;
use Http\HttpHeaderCollection;



class MailModule extends Module {


	
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



	





	public function testMail($p1) {

		$user = current_user();
		$to = $user->getEmail();

		if($p1 == "default") return "";
		
		list($module,$template) = $this->parseTemplate($p1);

		$class = $this->loadMailClass($module);

		$list = $class->getMessages();


		foreach($list as $message) {
			$message->setTo($to);
		}


		return $list;
	}





	public function sendMail($p1) {

		$user = current_user();
		$cc = $user->getEmail();

		if($p1 == "default") return "";
		
		list($module,$template) = $this->parseTemplate($p1);

		$class = $this->loadMailClass($module);

		return $class->getMessages();
	}





	/**
	 * Get MailMessage objects by delegating processing
	 * to the underlying Mail implementation.
	 */
	public function parseTemplate($template) {

		$req = $this->getRequest();
		$body = $req->getBody();


		$tmp = explode("-", $template);
		$module = array_shift($tmp);
		$template = implode("-", $tmp);

	
		return array($module,$template);
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