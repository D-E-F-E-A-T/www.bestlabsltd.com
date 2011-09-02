<?php
/**
* Administration consola controller.
*
* @log created 2011/AUG/23 23:13
*/
class consolaControl extends Control{

	public function consola(){
		# if not logged in, show login form.
		$this->common();
		$this->view->tag_title = "Hola mundo";
		$this->view->tag_jsend(PUB_URL.'consola.js');
	}

	public function logout(){
		$this->common();
		$this->model->auth->logout();
		$this->reload();
	}

	public function auth(){
		$this->view->tag_title = "Acceso a la Consola";
		$this->view->tag_jsend(PUB_URL.'consola.auth.js');
		$this->view->tag_link('stylesheet',PUB_URL.'consola.auth.css');
		# if logged in reload app.
		if ($this->model->auth->login()) $this->reload();
		$this->view->render('auth');
	}

	private function common(){
		$this->view->tag_link('stylesheet',PUB_URL.'ui/ui.css');
		$this->view->tag_jsend(PUB_URL.'ui/ui.js');
		if (!$this->model->auth->logged) return $this->auth();
	}
}
