<?php
/**
* consola Model
*
* @created 2011/AUG/23 23:18
*/
class consolaModel extends Model{

	private $languages = null;

	private $image_width  = 500;
	private $image_tmpath = 'consola/tmp/';  // on PUB  
	private $image_orig   = 'consola/orig/'; // on PUB 
	private $image_path   = 'consola/';      // on PUB 
	public  $image;


	/**
	 * Pseudo constructor
	 * @created 2011/AUG/24 00:32
	 */
	public function consola(){
		#enable authentication library.
		Auth::model($this);
		# make sure image  paths exists.
		$this->image_tmpath = PUB.$this->image_tmpath;
		$this->image_path   = PUB.$this->image_path;
		$this->image_orig   = PUB.$this->image_orig;
		if (!file_exists($this->image_tmpath)) mkdir($this->image_tmpath, 0777, true);
		if (!file_exists($this->image_orig))   mkdir($this->image_orig,   0777, true);
		if (!file_exists($this->image_path))   mkdir($this->image_path,   0777, true);
	}


	/**
	 * Get All languages in array form.
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
	 * @created 2011/SEP/15 01:59
	 */
	public function categories(){
		return $this->db->select('category', '*','GROUP BY `class` ORDER BY `class` DESC');
	}

	/**
	 * @created 2011/SEP/16 05:41
	 */
	public function products(){
		return $this->db->select('product','*','GROUP BY `class` ORDER BY `class` DESC');
	}


	/**
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
	 * @created 2011/SEP/15 00:45
	 */
	public function category_check(){
		if (!isset($_POST['value'])) return false;
		$val = $_POST['value'];
		if ($a = $this->db->select('category','class','class=? LIMIT 1',$val)) return 'found';
		return true;
	}

	/**
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
		foreach($_POST as $key => $val) $$key = $val;

		if (
			!file_exists($path = $this->image_tmpath.$file)                                     ||
			!($ext = pathinfo($file, PATHINFO_EXTENSION))                                       ||
			!file_exists($orig = $this->image_tmpath.str_replace('.'.$ext,'',$file).'.orig.'.$ext)
		) return "Imagen temporal corrupta, subir una nueva.";
		$ext = '.'.$ext;

		# generate urls
		$es_categ = $this->db->select('category','name','lang=? AND class=? LIMIT 1','es', $category);
		$es_categ = Utils::urlify($es_categ);
		$es_uname = Utils::urlify($es_name);
		$es_image = PUB_URL."es/$es_categ/$es_uname$ext";
		$es_path   = "es/$es_categ/$es_uname";
		$en_categ = $this->db->select('category','name','lang=? AND class=? LIMIT 1','en', $category);
		$en_categ = Utils::urlify($en_categ);
		$en_uname = Utils::urlify($en_name);
		$en_image = PUB_URL."en/$en_categ/$en_uname$ext";
		$en_path   = "en/$en_categ/$en_uname";
		# generate images
		try {
			# master images will preserve classname.
			rename($orig, ($orig = $this->image_orig.$class.$ext));
			rename($path, ($path = $this->image_path.$class.$ext));
			# public images will be symlinks
			$image_path_full = str_replace(PUB_URL, PUB, $en_image);
			$image_path_dir  = pathinfo($image_path_full, PATHINFO_DIRNAME);
			if (!file_exists($image_path_dir)) mkdir($image_path_dir, 0777, true);
			symlink($path, $image_path_full);
			$image_path_full = str_replace(PUB_URL, PUB, $es_image);
			$image_path_dir  = pathinfo($image_path_full, PATHINFO_DIRNAME);
			if (!file_exists($image_path_dir)) mkdir($image_path_dir, 0777, true);
			symlink($path, $image_path_full);
		} catch (Exception $e) { return 'Error al procesar imágen.'; }
		# insert data;
		$this->db->insert('product',array(
			array(
				'lang'  => 'es',
				'categ' => $category,
				'class' => $class,
				'image' => $es_image,
				'path'  => $es_path,
				'name'  => $es_name,
				'cont'  => $es_cont,
				'keyw'  => $es_keyw,
				'desc'  => $es_desc
			),
			array(
				'lang'  => 'en',
				'categ' => $category,
				'class' => $class,
				'image' => $en_image,
				'path'  => $en_path,
				'name'  => $en_name,
				'cont'  => $en_cont,
				'keyw'  => $en_keyw,
				'desc'  => $en_desc
			)
		));
		return true;
	}

	/**
	 * @created 2011/SEP/15 16:51
	 */
	public function product_check(){
		if (!isset($_POST['value'])) return false;
		$val = $_POST['value'];
		if ($this->db->select('product','class','class=? LIMIT 1',$val)) return 'found';
		return true;
	}

	/**
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
		$ext = pathinfo($_SERVER['HTTP_X_FILE_NAME'], PATHINFO_EXTENSION);
		if (!$ext) $ext = '.'.strtolower(str_replace('image/', '', $type));
		else $ext = '.'.$ext;
		$id = str_replace('.', '', (string)BMK);
		// save original and reduced;
		$orig = $this->image_tmpath.$id.'.orig'.$ext;
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

