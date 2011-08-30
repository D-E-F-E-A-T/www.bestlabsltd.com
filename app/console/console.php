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

	public function auth(){
		$this->view->tag_title = "Acceso a la Consola";
		$this->view->tag_jsend(PUB_URL.'console.auth.js');
		# if logged in reload app.
		if ($this->model->auth->login()) $this->reload();
		$this->view->render('auth');
	}

	private function common(){
		$this->view->tag_link('stylesheet',PUB_URL.'jqui-theme/jquery-ui-1.8.7.custom.css');
		$this->view->tag_jsend(PUB_URL.'jquery-ui-1.8.16.custom.min.js');
		$this->view->tag_jsend(PUB_URL.'console.jqui.js');
		# if not logged in, show login form.
		if (!$this->model->auth->logged) return $this->auth();
	}

}