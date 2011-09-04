<?php
/**
* Administration consola controller.
*
* @log created 2011/AUG/23 23:13
*/
class consolaControl extends Control{

	public $notfound = "La página que ingresó, no existe.";

	public function consola(){
		$this->common();
		# default action
		$this->view->class = "ver-producto";
		$this->ver_producto();
	}

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @created 2011/SEP/04 00:26
	 */
	public function agregar($type=false){
		$this->common();
		$this->route($type);
	}

	/**
	 * @created 2011/SEP/04 01:14
	 */
	public function ver($type=false){
		$this->common();
		$this->route($type);
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

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @created 2011/SEP/04 01:16
	 */
	private function ver_producto(){
		$this->view->tag_title = $this->view->title = 'Productos';
		$this->view->render('ver.producto');
	}

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @created 2011/SEP/04 00:35
	 */
	private function agregar_producto(){
		$this->view->tag_title = $this->view->title = 'Agregar Producto';
		# if no post is sent, just render the view;
		if (empty($_POST)) $this->view->render('agregar.producto');
		stop('proces product');
	}

	private function common(){
		$this->view->tag_link('stylesheet',PUB_URL.'ui/ui.css');
		$this->view->tag_jsend(PUB_URL.'ui/ui.js');
		if (!$this->model->auth->logged) return $this->auth();
		$this->view->tag_jsend(PUB_URL.'consola.js');
	}

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @created 2011/SEP/04 00:54
	 */
	private function route($method){
		# if no sub option provided, load default.
		if (!is_string($method)) $this->reload();
		# obtain calling method name, the old fucking way [I hate this].
		$bt = debug_backtrace();
		$this->view->class = $method = "{$bt[1]['function']}_{$method}";
		# if private method is defined run it. else, show error.
		if (method_exists($this, $method)) $this->$method();
		parent::error_404($this->notfound);
	}
}
