<?php
/**
 * Provides an automatized way of handling user authentication.
 *
 * @created 2011/AUG/25 17:48
 */
abstract class Auth  extends Application_Common {

	private static $model = null;
	private static $view  = null;

	/**
	 * Auth Model driver load.
	 *
	 * @created 2011/AUG/25 17:49
	 */
	final public static function model(&$app=false){
		# instanced from a model?
		if (!parent::is_model($app))
			error('Argument must contain a Model instance.');
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
				error('Could not find Database schema.');
			if (!$db->import($path)) error('Import failed.');
		}
		# include Model Instance Class
		if (!file_exists($path = strtolower(AUTH.__CLASS__.'.model'.EXT)))
			error('Internal Model class missing.');
		include $path;
		# instantiate and set,
		self::$model = new modelAuth($db);
		$app->auth = &self::$model;
		return true;
	}

	/**
	 * Auth View driver load.
	 *
	 * @created 2011/AUG/27 01:59
	 */
	final public static function view(&$app=false){
		# instanced from a view? 
		if(!parent::is_view($app))
			error('Argument must contain a View instance.');
		# make sure model has been instanced
		if (!self::$model instanceof AuthModelInstance)
			error('Auth Model was not detected.');
		# instantiate and set
		self::$view = new viewAuth();
		$app->auth = &self::$view;
		return true;
	}

}