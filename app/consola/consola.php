<?php
/**
 * Administration consola controller.
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
 * @created 2011/AUG/23 23:13
 */
class consolaControl extends Control{

	/**
	 * Constructor
	 * @created 2011/AGO/23 23:14
	 */
	public function consola(){
		$this->common();
		# default action
		$this->view->class = "ver_producto";
		$this->ver_producto();
	}

	/**
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

	/**
	 * Close sesion.
	 * @created 2011/SEP/02 17:12
	 */
	public function logout(){
		$this->common();
		$this->model->auth->logout();
		$this->reload();
	}

	/**
	 * Shows login form.
	 * @created 2011/AUG/29 23:43
	 */
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


####################################################################################################

	/**
	 * @created 2011/SEP/04 01:16
	 */
	private function ver_producto(){
		$this->view->tag_title = $this->view->title = 'Productos';
		$this->view->render('ver.producto');
	}

	/**
	 * @created 2011/SEP/04 00:35
	 */
	private function agregar_producto(){
		// is the user trying to upload a file?
		if (isset($_SERVER['HTTP_X_FILE_NAME']) && isset($_SERVER['CONTENT_LENGTH'])){
			if ( ($response = $this->model->product_image()) === true)
				stop('{ "image" : "'.$this->model->image.'" }');
			parent::error_500($response);
		}
		# if no post is sent, just render the view;
		if (empty($_POST)) {
			$this->view->tag_title = $this->view->title = 'Agregar Producto';
			$this->view->render('agregar.producto');
		}
		# a product check request
		if (isset($_POST['action'])){
			if (!($response = $this->model->product_check())) parent::error_500('Invalid Value');
			# an empty response means all ok.
			if ($response === true) stop();
			stop('found');
		}
		# a product add request.
		if ( ($response = $this->model->product_add()) === true) stop('Producto agregado con éxito.');
		parent::error_500($response);
	}

	/**
	 * @created 2011/SEP/04 13:41
	 */
	private function agregar_categoria(){
		# if no post is sent, just render the view;
		if (empty($_POST)) {
			$this->view->tag_title = $this->view->title = 'Agregar Categoría';
			$this->view->render('agregar.categoria');
		}
		# a category check request
		if (isset($_POST['action'])){
			if (!($response = $this->model->category_check())) parent::error_500('Invalid Value');
			# an empty response means all ok.
			if ($response === true) stop();
			stop('found');
		}
		# a categorory add request.
		if ( ($response = $this->model->category_add()) === true) stop('Categoría agregada con éxito.');
		parent::error_500($response);
	}

	/**
	 * @created 2011/SEP/16 06:45
	 */
	private function agregar_mercancia(){
		# if no post is sent, just render the view;
		if (empty($_POST)) {
			$this->view->tag_title = $this->view->title = 'Agregar Mercancía';
			$this->view->render('agregar.mercancia');
		}
	}


####################################################################################################

	/**
	 * Common procedures.
	 * @created 2011/AGO/23 23:14
	 */
	private function common(){
		$this->view->tag_link('stylesheet',PUB_URL.'ui/ui.css');
		$this->view->tag_jsini(PUB_URL.'ui/ui.js');
		if (!$this->model->auth->logged) return $this->auth(false);
		# this will happen only if user logged in.
		$this->view->tag_jsini(PUB_URL.'consola.js');
		$this->view->languages  = $this->model->languages();
		$this->view->categories = $this->model->categories();
		$this->view->products   = $this->model->products();
	}

	/**
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
		parent::error_404("La página que ingresó, no existe.");
	}


}

