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
	 * @author Hecdtor Menendez <h@cun.mx>
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
	 * @created 2011/SEP/15 01:59
	 */
	public function categories(){
		return $this->db->select('category', 'class','GROUP BY class ORDER BY class DESC');
	}

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @licence http://etor.mx/licence.txt
	 * @created 2011/SEP/14 23:24
	 */
	public function category_add(){
		if (!isset($_POST['es_name']) || !isset($_POST['en_name'])) return "Faltan Datos.";
		$this->db->insert('category', array(
			'lang'  => 'es',
			'class' => $_POST['class'],
			'name'  => $_POST['es_name']
		));
		$this->db->insert('category', array(
			'lang'  => 'en',
			'class' => $_POST['class'],
			'name'  => $_POST['en_name']
		));
		return true;
	}

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @licence http://etor.mx/licence.txt
	 * @created 2011/SEP/15 00:45
	 */
	public function category_check(){
		if (!isset($_POST['value'])) parent::error_500('Invalid Value.');
		$val = $_POST['value'];
		if ($this->db->select('category','class','class=? LIMIT 1',$val))
			echo 'found';
	}

}

