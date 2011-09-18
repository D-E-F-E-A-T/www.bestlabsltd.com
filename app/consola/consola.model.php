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

####################################################################################################

	/**
	 * @created 2011/SEP/15 01:59
	 */
	public function categories(){
		return $this->db->select('category', '*','GROUP BY `class` ORDER BY `class` DESC');
	}

	/**
	 * @created 2011/SEP/17 20:18
	 */
	public function category($class=false){
		if (!is_string($class)) return 'Clase inválida.';
		$qry = $this->db->select('category','*','class=? LIMIT 2', $class);
		if (!is_array($qry) || count($qry) != 2) return 'Clase inexistente.';
		$category = array();
		foreach($qry as $qry){
			$category[$qry['lang']] = $qry;
			unset($category[$qry['lang']]['lang']);
		}
		return $category;
	}

	/**
	 * @created 2011/SEP/17 11:51
	 */
	public function category_delete($target=false){
		$this->db->delete('category', 'class=?', $target);
		$this->db->delete('product',  'categ=?', $target);
		return true;
	}


	/**
	 * @created 2011/SEP/14 23:24
	 */
	public function category_add(){
		if (true !== $this->category_post_check()) return 'Faltan Datos';
		foreach($_POST as $key => $val) $$key = $val;
		$this->db->insert('category', array(
			array(
				'lang'  => 'es',
				'class' => $class,
				'name'  => $es_name,
				'desc'  => $es_desc,
				'keyw'  => $es_keyw
			),
			array(
				'lang'  => 'en',
				'class' => $class,
				'name'  => $en_name,
				'desc'  => $en_desc,
				'keyw'  => $en_keyw
			)
		));
		return true;
	}

	/**
	 * @created 2011/SEP/17 22:46
	 */
	public function category_update(){
		if (true !== $this->category_post_check()) return 'Faltan Datos';
		foreach($_POST as $key => $val) $$key = $val;
		$this->db->update('category', array(
			'name'  => $es_name,
			'desc'  => $es_desc,
			'keyw'  => $es_keyw
		), "lang='es' AND class=?", $class);
		$this->db->update('category', array(
			'name'  => $en_name,
			'desc'  => $en_desc,
			'keyw'  => $en_keyw
		), "lang='en' AND class=?", $class);
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

	private function category_post_check(){
		if (
				 count($_POST) != 7
			||	!isset($_POST['class'])
			||	!isset($_POST['es_name'])
			||	!isset($_POST['es_desc'])
			||	!isset($_POST['es_keyw'])
			||	!isset($_POST['en_name'])
			||	!isset($_POST['en_desc'])
			||	!isset($_POST['en_keyw'])
		) return false;
		return true;
	}

####################################################################################################

	/**
	 * @created 2011/SEP/16 05:41
	 */
	public function products(){
		return $this->db->select('product','*','GROUP BY `class` ORDER BY `class` DESC');
	}

	/**
	 * @created 2011/SEP/18 01:46
	 */
	public function product($class=false){
		if (!is_string($class)) return 'Clase inválida.';
		$qry = $this->db->select('product','*','class=? LIMIT 2', $class);
		if (!is_array($qry) || count($qry) != 2) return 'Clase inexistente.';
		$product= array();
		foreach($qry as $qry){
			$product[$qry['lang']] = $qry;
			unset($product[$qry['lang']]['lang']);
		}
		return $product;
	}

	/**
	 * @created 2011/SEP/17 12:00
	 */
	public function product_delete($target=false){
		return $this->db->delete('product', 'class=?', $target);
	}

	/**
	 * @created 2011/SEP/15 18:40
	 */
	public function product_add(){
		# "verify" data and gen vars
		if (!$this->product_post_check()) return "Faltan Datos.";
		foreach($_POST as $key => $val) $$key = $val;
		# generate urls
		foreach($this->product_urls() as $key => $val) $$key = $val;
		# obtain source file's path
		if (!is_array($tmp = $this->product_image_move($es_image, $en_image)))
			return 'Error procesando imagen.';
		foreach($tmp as $key => $val) $$key = $val;		
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

	public function product_update(){
		# "verify" data and gen vars
		if (!$this->product_post_check()) return "Faltan Datos.";
		foreach($_POST as $key => $val) $$key = $val;
		# generate urls
		foreach($this->product_urls() as $key => $val) $$key = $val;
		# obtain source file's path
		if (!is_array($tmp = $this->product_image_move($es_image, $en_image)))
			return 'Error procesando imagen.';
		foreach($tmp as $key => $val) $$key = $val;		
		# update data;
		$this->db->update('product', array(
			'categ' => $category,
			'image' => $es_image,
			'path'  => $es_path,
			'name'  => $es_name,
			'cont'  => $es_cont,
			'keyw'  => $es_keyw,
			'desc'  => $es_desc
		), "lang='es' AND class=?", $class);
		$this->db->update('product', array(
			'categ' => $category,
			'image' => $en_image,
			'path'  => $en_path,
			'name'  => $en_name,
			'cont'  => $en_cont,
			'keyw'  => $en_keyw,
			'desc'  => $en_desc
		), "lang='en' AND class=?", $class);
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

	/**
	 * @created 2011/SEP/18 07:22
	 */
	private function product_image_move($es_image, $en_image){
		try {
			if (substr($_POST['file'], 0, 8) == '__same__'){
				$file = str_replace('__same__', '',$_POST['file']);
				$path = $this->image_path.$file;
				$ext  = '.'.(string)pathinfo($path, PATHINFO_EXTENSION);
				$orig = $this->image_orig.$file;
			} else {
				$path = $this->image_tmpath . $_POST['file'];
				$ext  = '.'.(string)pathinfo($path, PATHINFO_EXTENSION);
				$orig = $this->image_tmpath . str_replace($ext,'',$_POST['file']) . '.orig'.$ext;
				rename($orig, ($orig = $this->image_orig.$_POST['class'].$ext));
				rename($path, ($path = $this->image_path.$_POST['class'].$ext));
			}
		} catch (Exception $e) { return false; }
		$en_image.=$ext;
		$es_image.=$ext;
		# generate symlinks
		try {
			# english
			$image_path_full = str_replace(PUB_URL, PUB, $en_image);
			$image_path_dir  = pathinfo($image_path_full, PATHINFO_DIRNAME);
			if (!file_exists($image_path_dir)) mkdir($image_path_dir, 0777, true);
			if (file_exists($image_path_full)) unlink($image_path_full);
			symlink($path, $image_path_full);
			# spanish
			$image_path_full = str_replace(PUB_URL, PUB, $es_image);
			$image_path_dir  = pathinfo($image_path_full, PATHINFO_DIRNAME);
			if (!file_exists($image_path_dir)) mkdir($image_path_dir, 0777, true);
			if (file_exists($image_path_full)) unlink($image_path_full);
			symlink($path, $image_path_full);
		} catch (Exception $e) { return false; }
		# return updated images.
		return array(
			'en_image' => $en_image,
			'es_image' => $es_image
		);
	}


	/**
	 * @created 2011/SEP/18 05:46
	 */
	private function product_post_check(){
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
		) return false;
		return true;
	}

	/**
	 * @created 2011/SEP/18 06:04
	 */
	private function product_urls(){
		$es_name = Utils::urlify($_POST['es_name']);
		$es_cate = Utils::urlify($this->db->select(
			'category',
			'name',
			'lang=? AND class=? LIMIT 1','es', $_POST['category']
		));
		$en_name = Utils::urlify($_POST['en_name']);
		$en_cate = Utils::urlify($this->db->select(
			'category',
			'name',
			'lang=? AND class=? LIMIT 1','es', $_POST['category']
		));
		$es = "es/$es_cate/$es_name";
		$en = "es/$en_cate/$en_name";
		return array(
			'es_path'  => $es,
			'es_image' => PUB_URL.$es,
			'en_path'  => $en,
			'en_image' => PUB_URL.$en
		);
	}

}

