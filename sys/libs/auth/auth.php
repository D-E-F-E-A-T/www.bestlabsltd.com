<?php
/**
 * Provides an automatized way of handling user authentication.
 *
 * @created 2011/AUG/25 17:48
 */
abstract class Auth  extends Application_Common {

	private static $modelinstance = null;

	final public static function &view(&$app=false){
		echo "puto";
	}


	/**
	 * Does some checks before actually doing anything.
	 * @created 2011/AUG/25 17:49
	 */
	final public static function &model(&$app=false){
		# instanced from a model?
		if (!parent::is_model($app))
			error('Application must be provided and instantiated from a Model.');
		# check for tokens
		if (!defined('TOKEN_SECRET') || !defined('TOKEN_PUBLIC'))
		error('Model Tokens are required');
		# has valid database?
		if (!is_object($db = Model::db_look($app)))
			error('A database must be instantiated before loading this.');
		if ($db->driver !='mysql')
			error('Support for your driver is not yet implemented');
		# does an auth table exists on database? if not create it.
		if (!$db->is_table('auth')){
			if (!file_exists($path = strtolower(AUTH.__CLASS__.'.'.$db->driver.'.sql')))
				error("Could not find Database schema.");
			if (!$db->import($path)) error('Import failed.');
		}
		$instance = new AuthModelInstance($db);
		return $instance;
	}
}

/**
 * @created 2011/AUG/26 08:39
 */
final class AuthModelInstance extends Library {

	private $db    = null;

	public $logged = null;
	public $pass   = null;

	/**
	 * @created 2011/AUG/26 08:40
	 */
	public function __construct(&$db){
		if (!self::samefile()) error('Direct Instancing is disabled.');
		$this->db = &$db;
		# record found for this UUID assume usser is logged and store password.
		$this->pass   = $db->select('auth', 'pass', 'uuid=? LIMIT 1',UUID);
		$this->logged = (bool)$this->pass;
	}

	/**
	 * @created 2011/AUG/26 8:45
	 */
	public function login(){
		stop('implement this');
	}

	/**
	 * @created 2011/AUG/26 8:48
	 */
	public function logout(){
		stop('implement this');
	}

}