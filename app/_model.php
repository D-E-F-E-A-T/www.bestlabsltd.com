<?php
class Model extends Application_Model {

	public function _construct(){
		# connect to database.
		$this->db = DB::mysql('bestlabs','Herector23=&.');
		# if database is empty fill it with our default SQL statements.
		// if ($this->db->is_empty()) $this->db->import(APP.'database.sql');
		# Enable TOKENS for site, halt if not set correctly.
		if (!$this->token()) parent::error_403('Invalid Data.');
	}

}