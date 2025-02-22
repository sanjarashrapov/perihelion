<?php

final class StateController implements StateControllerInterface {
	
	private $urlArray;
	private $inputArray;
	private $moduleArray;
	private $errorArray;
	private $messageArray;

	public function __construct($urlArray, $inputArray, $moduleArray) {

		$this->urlArray = $urlArray;
		$this->inputArray = $inputArray;
		$this->moduleArray = $moduleArray;
		$this->errorArray = array();
		$this->messageArray = array();

	}

	public function setState() {

		$loc = $this->urlArray;
		$input = $this->inputArray;
		$mods = $this->moduleArray;
		
		if ($loc[0] == 'enquiry') { $state = new EnquiryController($loc,$input,$mods); }
		if ($loc[0] == 'designer') { $state = new DesignerController($loc,$input,$mods); }
		if ($loc[0] == 'manager') { $state = new ManagerController($loc,$input,$mods); }
		if ($loc[0] == 'newsletter') { $state = new NewsletterController($loc,$input,$mods); }
		if ($loc[0] == 'admin') { $state = new AdminController($loc,$input,$mods); }
		if ($loc[0] == 'profile') { $state = new ProfileController($loc,$input,$mods); }
		if ($loc[0] == 'support') { $state = new SupportController($loc,$input,$mods); }
		if ($loc[0] == 'contact') { $state = new ContactController($loc,$input,$mods); }
		
		$authStates = array('account-recovery','login','logout','reset-password');
		if (in_array($loc[0],$authStates)) { $state = new AuthController($loc,$input,$mods); }
		
		foreach ($this->moduleArray AS $moduleName) {
			if ($loc[0] == $moduleName) {
				$moduleStateController = ModuleUtilities::moduleToClassName($moduleName, 'Controller');
				if (class_exists($moduleStateController)) {
					$state = new $moduleStateController($loc,$input,$mods);
				}
		    }
		}

		if (isset($state)) {

			$state->setState();

			foreach ($this->moduleArray AS $moduleName)  {
				$moduleSessionController = ModuleUtilities::moduleToClassName($moduleName, 'SessionController');
				if (class_exists($moduleSessionController)) {
					$moduleSessionData = new $moduleSessionController();
					$moduleSessionData->setSession();
				}
			}

			$this->errorArray = $state->getErrors();
			$this->messageArray = $state->getMessages();

		}

	}
	
	public function getErrors() {
	    return $this->errorArray;
	}
	
	public function getMessages() {
		if (Config::read('environment') == 'dev') {
			$this->messageArray[] = Lang::getLang('devEnvironment') . ' [' . $_SERVER['HTTP_HOST'] . ']';
		}
	    return $this->messageArray;
	}

}

?>