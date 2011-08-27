<?php
class Application_Control extends Library {

	public $view;
	public $model;

	/**
	 * Control Constuctor
	 * @created 2011/AUG/26 20:25
	 */
	final public function __construct(){
		# if run a pseudo constructor if exist.
		if (method_exists($this, '_construct') && is_callable(array($this,'_construct'))) 
			return $this->_construct();
	}
}