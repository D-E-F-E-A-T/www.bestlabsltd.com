<?php
/**
* Administration Console controller.
*
* @log created 2011/AUG/23 23:13
*/
class consoleControl extends Control{


	public function console(){
		$this->view->tag_title ="Hola mundo";
		stop($this->model->auth);
	}


}