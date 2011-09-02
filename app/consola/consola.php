<?php
/**
* Administration consola controller.
*
* @log created 2011/AUG/23 23:13
*/
class consolaControl extends Control{

	public function consola(){
		# if not logged in, show login form.
		if (!$this->model->auth->logged) return $this->auth();
		$this->common();
		$this->view->tag_title = "Hola mundo";
		$this->view->tag_jsend(PUB_URL.'consola.js');
	}

	public function auth(){
		$this->common();
		$this->view->tag_title = "Acceso a la Consola";
		$this->view->tag_jsend(PUB_URL.'consola.auth.js');
		$this->view->tag_link('stylesheet',PUB_URL.'consola.auth.css');
		# if logged in reload app.
		if ($this->model->auth->login()) $this->reload();
		$this->view->render('auth');
	}

	private function common(){
		#$this->view->tag_link('stylesheet',PUB_URL.'aristo/jquery-ui-1.8.7.custom.css');
		#$this->view->tag_link('stylesheet',PUB_URL.'consola.css');
		$this->view->tag_link('stylesheet',PUB_URL.'ui/ui.css');
		$this->view->tag_jsend(PUB_URL.'ui/ui.js');
	}
}
