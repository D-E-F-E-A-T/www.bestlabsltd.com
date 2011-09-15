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

	public function test(){
		stop($_POST);
		if (
			!isset($_SERVER['HTTP_X_FILE_NAME']) ||
			!isset($_SERVER['CONTENT_LENGTH'])
		) parent::error_403();

		if (!(int)$_SERVER['CONTENT_LENGTH']) {
			Core::header(500);
			stop('Upload Failed');
		}
		stop('Upload Succeeded.');
	}

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @licence http://etor.mx/licence.txt
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

	public function auth($loadcommon=true){
		# if called directly, call common.
		if ($loadcommon) $this->common();
		$this->view->tag_title = "Acceso a la Consola";
		$this->view->tag_jsini(PUB_URL.'consola.auth.js');
		$this->view->tag_link('stylesheet',PUB_URL.'consola.auth.css');
		# if logged in reload app.
		if ($this->model->auth->login()) $this->reload();
		$this->view->render('auth');
	}

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @licence http://etor.mx/licence.txt
	 * @created 2011/SEP/04 01:16
	 */
	private function ver_producto(){
		$this->view->tag_title = $this->view->title = 'Productos';
		$this->view->render('ver.producto');
	}

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @licence http://etor.mx/licence.txt
	 * @created 2011/SEP/04 00:35
	 */
	private function agregar_producto(){
		$this->view->tag_title = $this->view->title = 'Agregar Producto';
		# if no post is sent, just render the view;
		if (empty($_POST)) $this->view->render('agregar.producto');
		stop('proces product');
	}

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @licence http://etor.mx/licence.txt
	 * @created 2011/SEP/04 13:41
	 */
	private function agregar_categoria(){
		$this->view->tag_title = $this->view->title = 'Agregar Categoría';
		# if no post is sent, just render the view;
		if (empty($_POST)) $this->view->render('agregar.categoria');
		# an akax  category check request
		if (isset($_POST['action'])){
			$this->model->category_check();
			stop();
		}
		# an empty response will tell the client everything went as expected.
		if ( ($response = $this->model->category_add()) === true) stop();
		parent::error_500($response);
	}

	private function common(){
		$this->view->tag_link('stylesheet',PUB_URL.'ui/ui.css');
		$this->view->tag_jsini(PUB_URL.'ui/ui.js');
		if (!$this->model->auth->logged) return $this->auth(false);
		# this will happen only if user logged in.
		$this->view->tag_jsini(PUB_URL.'consola.js');
		$this->view->languages  = $this->model->languages();
		$this->view->categories = $this->model->categories();
	}

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @licence http://etor.mx/licence.txt
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
