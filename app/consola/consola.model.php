<?php
/**
* consola Model
*
* @created 2011/AUG/23 23:18
*/
class consolaModel extends Model{

	private $languages = null;

	/**
	 * Pseudo constructor
	 *
	 * @author Hector Menendez <h@cun.mx>
	 * @licence http://etor.mx/licence.txt
	 * @created 2011/AUG/24 00:32
	 */
	public function consola(){
		#enable authentication library.
		Auth::model($this);
	}


	/**
	 * Get All languages in array form.
	 *
	 * @author Hector Menendez <h@cun.mx>
	 * @licence http://etor.mx/licence.txt
	 * @created 2011/SEP/04 13:50
	 */
	public function languages(){
		if (is_array($this->languages)) return $this->languages;
		$languages = array();
		foreach($this->db->select('language',false, 'ORDER BY id DESC') as $l)
			$languages[$l['id']] = $l['name'];
		return $this->languages = $languages;
	}

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @licence http://etor.mx/licence.txt
	 * @created 2011/SEP/04 14:28
	 */
	public function categories(){

	}
}

