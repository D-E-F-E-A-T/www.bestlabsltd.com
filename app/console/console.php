<?php
/**
* Administration Console controller.
*
* @log created 2011/AUG/23 23:13
*/
class consoleControl extends Control{


	public function console(){
		$this->common();
		$this->view->tag_title ="Hola mundo";
	}

	private function common(){
		if (!$this->model->auth->logged) return $this->auth();
	}

	public function auth(){
		# if not logged in or login failed, present login form.
		if (!$this->model->auth->login()){
			$this->view->tag_title = "Acceso a la Consola";
			$this->view->render('login');
		}
		$this->reload();
	}

}