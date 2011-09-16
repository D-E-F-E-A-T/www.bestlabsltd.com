<?php
/**
* consola Model
*
* @created 2011/AUG/23 23:18
*/
class consolaModel extends Model{

	private $languages = null;

	private $image_width  = 500;
	private $image_tmpath = 'consola/upload/'; // on PUB 
	public  $image;


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
		$this->image_tmpath = PUB.$this->image_tmpath;
		if (!file_exists($this->image_tmpath)) mkdir($this->image_tmpath, 0777, true);
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
			array(
				'lang'  => 'es',
				'class' => $_POST['class'],
				'name'  => $_POST['es_name']
			),
			array(
				'lang'  => 'en',
				'class' => $_POST['class'],
				'name'  => $_POST['en_name']
			)
		));
		return true;
	}

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @licence http://etor.mx/licence.txt
	 * @created 2011/SEP/15 00:45
	 */
	public function category_check(){
		if (!isset($_POST['value'])) return false;
		$val = $_POST['value'];
		if ($a = $this->db->select('category','class','class=? LIMIT 1',$val)) return 'found';
		return true;
	}

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @licence http://etor.mx/licence.txt
	 * @created 2011/SEP/15 18:40
	 */
	public function product_add(){
		# "verify" data
		if(
				 count($_POST) != 11
			||	!isset($_POST['category'])
			||	!isset($_POST['class'])
			||	!isset($_POST['file'])
			||	!isset($_POST['en_cont'])
			||	!isset($_POST['en_desc'])
			||	!isset($_POST['en_keyw'])
			||	!isset($_POST['en_name'])
			||	!isset($_POST['es_cont'])
			||	!isset($_POST['es_desc'])
			||	!isset($_POST['es_keyw'])
			||	!isset($_POST['es_name'])
		) return 'Faltan Datos.';
		# insert data;
		$this->db->insert('product',array(
			array(
				'lang'  => 'es',
				'categ' => $_POST['category'],
				'class' => $_POST['class'],
				'name'  => $_POST['es_name'],
				'cont'  => $_POST['es_cont'],
				'keyw'  => $_POST['es_keyw'],
				'desc'  => $_POST['es_desc']
			),
			array(
				'lang'  => 'en',
				'categ' => $_POST['category'],
				'class' => $_POST['class'],
				'name'  => $_POST['en_name'],
				'cont'  => $_POST['en_cont'],
				'keyw'  => $_POST['en_keyw'],
				'desc'  => $_POST['en_desc']
			)
		));
		return true;
	}

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @licence http://etor.mx/licence.txt
	 * @created 2011/SEP/15 16:51
	 */
	public function product_check(){
		if (!isset($_POST['value'])) return false;
		$val = $_POST['value'];
		if ($this->db->select('product','class','class=? LIMIT 1',$val)) return 'found';
		return true;
	}

	/**
	 * @author Hector Menendez <h@cun.mx>
	 * @licence http://etor.mx/licence.txt
	 * @created 2011/SEP/15 04:15
	 */
	public function product_image(){
		if (!(int)$_SERVER['CONTENT_LENGTH']) return 'Transferencia fallida.';
		if (
			!isset($_SERVER['HTTP_X_FILE_NAME']) ||
			!isset($_SERVER['HTTP_X_FILE_TYPE']) ||
			!isset($_SERVER['HTTP_X_FILE_SIZE'])
		) 	return 'Headers inválidos.';
		# shorthands
		$name = $_SERVER['HTTP_X_FILE_NAME'];
		$type = $_SERVER['HTTP_X_FILE_TYPE'];
		$size = $_SERVER['HTTP_X_FILE_SIZE'];
		$data = file_get_contents('php://input');
		# we did the type checking on client-side.
		# TODO: Don't be lazy and sanitize here too, don't trust the client.
		$ext = '.'.strtolower(str_replace('image/', '', $type));
		$id = str_replace('.', '', (string)BMK);
		// save original and reduced;
		$orig = $this->image_tmpath.$id.'.original'.$ext;
		$save = $this->image_tmpath.$id.$ext;

		try {
			file_put_contents($orig, $data);
			// save a copy, reduce its size, sharpen it, and save it again.
			file_put_contents($save, $data);
			$img = Image::towidth($save, $this->image_width);
			Image::output($this->product_image_sharpen($img), $save);
		} catch (Exception $e) { parent::error_500($e->getMessage()); }
		$this->image = $id.$ext;
		return true;
	}

	private function &product_image_sharpen(&$img){
		# mild sharpen.
		$matrix = array( 
            array(-1.2, -01.0, -1.2), 
            array(-1.0, +20.0, -1.0), 
            array(-1.2, -01.0, -1.2) 
        );
		# Reference
		# http://loriweb.pair.com/8udf-sharpen.html
        # $matrix = array(-1,-1,-1,-1,16,-1,-1,-1,-1); // subtle sharpen.
        # calculate the sharpen divisor 
        $divisor = array_sum(array_map('array_sum', $matrix));
        $offset = 0; 
        // apply the matrix 
        imageconvolution($img, $matrix, $divisor, $offset);
        return $img;
	}

}

