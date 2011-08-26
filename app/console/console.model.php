<?php
/**
* Console Model
*
* @created 2011/AUG/23 23:18
*/
class consoleModel extends Model{
	

	/**
	 * Pseudo constructor
	 *
	 * @created 2011/AUG/24 00:32
	 */
	public function console(){
		stop();
		$this->token = TOKEN_PUBLIC;
		$this->session();
	}


	/**
	 * Authentication Management
	 *
	 * @return  mixed  BOOL stating wether the user is logged or not, 
	 *                 NULL if th user tried to login but failed.
	 * @created 2011/AUG/23 11:53
	 */
	public function session(){
		# UUID has session? 
		$qry = $this->db->select('session',false,'uuid=?',UUID);
		# no session, create one, it'll be valid for one run.
		if (empty($qry)){
			$this->db->insert('session',array(
				'uuid'  => UUID,
				'token' => TOKEN_SECRET,
				'date'  => time()
			));
			return false;
		}
		# there's a session available, but, is it active?
		# if it is, update public token with the valid one, and return true:
		if ($qry['logged']) return is_string($this->token = $qry['token']);
		# session is not active, verify if the user is trying to login.
		if (!$auth = $this->session_login($qry)){
			# user didn't provide valid credentials, remove this session and 
			# let them try again, with a new token.
			$this->session_kill();
			$this->session();
			return null;
		}
		# user logged correctly, update session.
		$sql = 'UPDATE session SET id_user=?, logged=1 WHERE uuid=%s';
		$this->db->exec($sql, $user['user'],$qry['uuid']);
	}

	public function session_login(){
		# is the user even trying to login?
		if (!defined('TOKEN_SECRET') || empty($_POST)) return false;
		#if (
		#	!isset($_POST['token']) ||
		#	!isset($_POST['user'] ) ||
		#	!isset($_POST['pass'] ) ||
		#	$session['key'] !== Utils::cryptor('decrypt',$_POST['SESSION_KEY'])
	}

}

