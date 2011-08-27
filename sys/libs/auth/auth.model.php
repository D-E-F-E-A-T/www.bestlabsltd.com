<?php
/**
 * @created 2011/AUG/26 08:39
 */
final class modelAuth extends Library {

	private $db    = null;

	public $logged = null;
	public $pass   = null;

	/**
	 * @created 2011/AUG/26 08:40
	 */
	public function __construct(&$db){
		if (strtolower((string)parent::class_calling()) != 'auth')
			error('Direct Instancing is disabled.');
		$this->db = &$db;
		# record found for this UUID assume usser is logged and store password.
		$this->pass   = $db->select('auth', 'pass', 'uuid=? LIMIT 1',UUID);
		$this->logged = (bool)$this->pass;
	}

	/**
	 * @created 2011/AUG/26 8:45
	 */
	public function login(){
		# if no post data is being sent; do nothing.
		if (empty($_POST)) return false;
		stop($_POST);
	}

	/**
	 * @created 2011/AUG/26 8:48
	 */
	public function logout(){
		stop('implement this');
	}

}