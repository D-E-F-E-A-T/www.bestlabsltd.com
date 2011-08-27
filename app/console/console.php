<?php
/**
* Administration Console controller.
*
* @log created 2011/AUG/23 23:13
*/
class consoleControl extends Control{


	public function console(){
		if (!$this->model->auth->logged) return $this->login();
		$this->view->tag_title ="Hola mundo";
	}

	private function login(){
		$this->view->tag_title = "Acceso a la Consola";

		$this->view->render('login');
	}

}