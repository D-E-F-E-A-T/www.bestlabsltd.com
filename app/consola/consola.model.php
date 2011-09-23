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
	 * @created 2011/SEP/18 01:46
	 */
	public function products(){
		return $this->db->select('product','*','GROUP BY `class` ORDER BY `class` DESC');
	}


####################################################################################################

	/**
	 * @created 2011/SEP/19 02:31
	 */
	public function password_change(){
		if (
			 empty($_POST)
		||	 count($_POST) != 3
		||	!isset($_POST['pass_orig'])
		||	!isset($_POST['pass_new'])
		||	!isset($_POST['pass_conf'])
		) return 'Datos Incorrectos';
		if ($_POST['pass_new'] !== $_POST['pass_conf'])
			return 'La confirmación de constraseña, no concuerda.';
		if (!$a = $this->auth->password($_POST['pass_orig']))
			return 'Contraseña actual inválida.';
		# everything in order, change password.
		if (true !== $this->auth->password(true, $_POST['pass_new']))
			return 'Error desconocido.';
		return true;
	}
####################################################################################################


	/**
	 * @created 2011/SEP/16 05:41
	 */
	public function stocks(){
		return $this->db->query(
		'  SELECT
				  product,
				  expires,
				  COUNT(*)                                   AS total,
				  SUM(CASE WHEN printed=1 THEN 1 ELSE 0 END) AS actived,
				  SUM(CASE WHEN valided=1 THEN 1 ELSE 0 END) AS valided
			 FROM stock
			WHERE expires > CURDATE()
		 GROUP BY product, expires
		 ORDER BY expires DESC
		'
		);
	}
	/**
	 *
	 * @created 2011/SEP/19 14:49
	 */
	public function stock_add(){
		if (
			 empty($_POST)
		||	 count($_POST) != 3
		||	!isset($_POST['product'])
		||	!isset($_POST['cantidad'])
		||	!isset($_POST['expires'])
		) return 'Datos Incorrectos';
		# obtain latest id from database.
		$id = $this->db->select('stock','id','ORDER BY `id` DESC LIMIT 1');
		if (empty($id)) $id = 0;
		else $id = $this->stock_id_decode($id);
		if ($id === false) return "No fue posible determinar el índice del stock.";
		$id++;
		$stock = array();
		foreach(range($id, $id+((int)$_POST['cantidad']-1)) as $id) $stock[] = array(
			'id'      => $this->stock_id_encode($id),
			'product' => $_POST['product'],
			'created' => date(DATE_W3C),
			'expires' => $_POST['expires']
		);
		$this->db->insert('stock', $stock);
		return true;
	}
	private $stock_config = array(
		'cols'          => '8',
		'rows'          => '14',
		'font'          => '06.00',
		'scale'         => '00.46',
		'width'         => '22.50',
		'height'        => '15.30',
		'margin-left'   => '01.00',
		'margin-top'    => '00.20',
		'margin-right'  => '00.90',
		'margin-bottom' => '00.10',
		'space-x'       => '00.20',
		'space-y'       => '00.30'
	);
	/**
	 * @created 2011/SEP/20 09:15
	 */
	public function stock_config_load(){
		$path = APP_PATH.'consola.admin.etiqueta.config';
		if (!file_exists($path)) {
			file_put_contents($path, serialize($this->stock_config));
			return $this->stock_config;
		}
		$arr = unserialize(file_get_contents($path));
		if (
			!isset($arr['cols'])
		||	!isset($arr['rows'])
		||	!isset($arr['font'])
		||	!isset($arr['scale'])
		||	!isset($arr['width'])
		||	!isset($arr['height'])
		||	!isset($arr['margin-left'])
		||	!isset($arr['margin-top'])
		||	!isset($arr['margin-right'])
		||	!isset($arr['margin-bottom'])
		||	!isset($arr['space-x'])
		||	!isset($arr['space-y'])
		) return false;
		return $arr;
	}
	/**
	 * @created 2011/SEP/20 09:29
	 */
	public function stock_config_save(){
		if (
			!isset($_POST['cols'])
		||	!isset($_POST['rows'])
		||	!isset($_POST['font'])
		||	!isset($_POST['scale'])
		||	!isset($_POST['width'])
		||	!isset($_POST['height'])
		||	!isset($_POST['margin-left'])
		||	!isset($_POST['margin-top'])
		||	!isset($_POST['margin-right'])
		||	!isset($_POST['margin-bottom'])
		||	!isset($_POST['space-x'])
		||	!isset($_POST['space-y'])
		) return 'Datos Incompletos.';
		file_put_contents(APP_PATH.'consola.admin.etiqueta.config', serialize($_POST));
		return true;
	}

	/**
	 * @created 2011/SEP/20 16:02
	 */
	public function stock_activate($product, $expires){
		# load fpdf
		define('FPDF_FONTPATH',APP_PATH.'fpdf');
		include APP_PATH.'fpdf/fpdf.php';
		# populate vars 
		foreach($this->stock_config_load() as $k=>$v) {
			$k = str_replace('-', '', $k);
			$$k = (float)$v;
		} 
		# define innerWidth and item dimentions
		$inwidth  = (($width  - $marginleft) - $marginright); //- (($cols-2)*$spacex)) / $cols;
		$inheight = (($height - $margintop)  - $marginbottom);
		$itemw = ($inwidth  - ($spacex*($cols-1))) / $cols;
		$itemh = ($inheight - ($spacey*($rows-1))) / $rows;
		# Setup PDF Document
		$pdf = new FPDF($width > $height? 'L' : 'P', 'cm', array($width, $height));
		$pdf->setMargins($marginleft, $margintop, $marginright);
		$pdf->setAutoPageBreak(true, 0);
		$pdf->AddFont('AndaleMono','','Andale_Mono.php');
		$pdf->SetFont('AndaleMono','', $font);
		$pdf->AddPage();
		# get the inactive stock
		$qry = $this->db->select(
			'stock',
			'id', 
			'product  = ? AND 
			 expires  = ? AND
			 printed <> 1 
			 ORDER BY created DESC', $product, $expires
		);
		$total = count($qry);
		if (!$total) return 'No existe mercancía sin validación en este lote.';
		# traverse elements
		for($i=0; $i<$total; $i++){
			$tmp = $i % $cols;
			# if multiple, print new line and vertical spacing.
			if ($i>($cols-1) && $tmp == 0) {
				$pdf->ln();
				$pdf->Cell($inwidth, $spacey,'',0);
				$pdf->ln();
			}
			$pdf->Cell($itemw,  $itemh, $qry[$i], 0, 0, 'C', 0);
			$pdf->Cell($spacex, $itemh, '', 0); #spacer
			# mark id as printed
			$this->db->update('stock', array('printed' => 1), 'id=?', $qry[$i]);
		}
		return $pdf;
	}


	/**
	 * Determines the random order id will obtain.
	 */
	private $stock_key = array(
		array(3,1,0,2),
		array(2,3,1,0),
		array(1,2,0,3),
		array(1,3,0,2),
		array(3,2,0,1),
		array(2,3,0,1),
		array(0,3,1,2),
		array(1,2,3,0),
		array(1,0,3,2),
		array(2,1,3,0)
	);
	private $stock_base34 = '0123456789ABCDEFGHIJKLMNOPQRSTUVWX';
	/**
	 * Determine the last available ID, to avoid collitions.
	 * It's embedded on the serial:
	 * Z{KEY}XXXXXXXXXXXXXY{ID}
	 * @created 2011/SEP/19 16:02
	 */
	private function stock_id_decode($id=false){
		$id = (string)$id;
		if (!preg_match("/Z(\d)/", $id, $key)) return false;
		$key   = $this->stock_key[$key[1]];
		$chunk = str_split($id, 4);
		$id    = array();
		$i     = 0;
		foreach($key as $key) $id[$key] = $chunk[$i++];
		ksort($id);
		$id = join('',$id);
		return (int)base_convert(substr($id, strpos($id, 'Y')+1), 34, 10);
	}
	/**
	 * generates a base34 string, and scrambles it to hide
	 * a little bit, the design pattern.
	 * @created 2011/SEP/19 16:24
	 */
	private function stock_id_encode($id=false){
		$id    = base_convert((int)$id, 10, 34);
		$len   = ($len = strlen($id)) + ($len%2);
		$id    = strtoupper(str_pad($id, $len, '0', STR_PAD_LEFT));
		$base  = '';
		foreach(range(0,12-$len) as $_) $base .= $this->stock_base34{mt_rand(0,33)};
		$rand  = mt_rand(0,9);
		$chunk = str_split("Z{$rand}{$base}Y{$id}",4);
		$id    = array();
		foreach($this->stock_key[$rand] as $i) $id[] = $chunk[$i];
		return join('', $id);
	}
####################################################################################################

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
				'url'   => Utils::urlify($es_name),
				'desc'  => $es_desc,
				'keyw'  => $es_keyw
			),
			array(
				'lang'  => 'en',
				'class' => $class,
				'name'  => $en_name,
				'url'   => Utils::urlify($en_name),
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
			'url'   => Utils::urlify($es_name),
			'desc'  => $es_desc,
			'keyw'  => $es_keyw
		), "lang='es' AND class=?", $class);
		$this->db->update('category', array(
			'name'  => $en_name,
			'url'   => Utils::urlify($en_name),
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
		if (!is_array($tmp = $this->product_image_move($es_url, $en_url)))
			return 'Error procesando imagen.';
		$es_url = explode('/', $es_url);
		$en_url = explode('/', $en_url);
		foreach($tmp as $key => $val) $$key = $val;
		# insert data;
		$this->db->insert('product',array(
			array(
				'lang'  => 'es',
				'categ' => $category,
				'class' => $class,
				'name'  => $es_name,
				'cont'  => $es_cont,
				'keyw'  => $es_keyw,
				'desc'  => $es_desc,
				'urli'  => $es_image,
				'urln'  => $es_url[1],
				'urlc'  => $es_url[0]
			),
			array(
				'lang'  => 'en',
				'categ' => $category,
				'class' => $class,
				'name'  => $en_name,
				'cont'  => $en_cont,
				'keyw'  => $en_keyw,
				'desc'  => $en_desc,
				'urli'  => $en_image,
				'urln'  => $en_url[1],
				'urlc'  => $en_url[0]
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
		if (!is_array($tmp = $this->product_image_move($es_url, $en_url)))
			return 'Error procesando imagen.';
		$es_url = explode('/', $es_url);
		$en_url = explode('/', $en_url);
		foreach($tmp as $key => $val) $$key = $val;
		# update data;
		$this->db->update('product', array(
			'categ' => $category,
			'name'  => $es_name,
			'cont'  => $es_cont,
			'keyw'  => $es_keyw,
			'desc'  => $es_desc,
			'urli'  => $es_image,
			'urln'  => $es_url[1],
			'urlc'  => $es_url[0]
		), "lang='es' AND class=?", $class);
		$this->db->update('product', array(
			'categ' => $category,
			'name'  => $en_name,
			'cont'  => $en_cont,
			'keyw'  => $en_keyw,
			'desc'  => $en_desc,
			'urli'  => $en_image,
			'urln'  => $en_url[1],
			'urlc'  => $en_url[0]
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
		$name = strtolower($_SERVER['HTTP_X_FILE_NAME']);
		$type = strtolower($_SERVER['HTTP_X_FILE_TYPE']);
		$size = $_SERVER['HTTP_X_FILE_SIZE'];
		$data = file_get_contents('php://input');
		# we did the type checking on client-side.
		# TODO: Don't be lazy and sanitize here too, don't trust the client.
		$ext = pathinfo($name, PATHINFO_EXTENSION);
		if (!$ext) $ext = '.'.str_replace('image/', '', $type);
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
	private function product_image_move($es_url, $en_url){
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
			# generate symlinks
			# english
			$en_image = PUB_URL."en/$en_url$ext";
			$image_path_full = str_replace(PUB_URL, PUB, $en_image);
			$image_path_dir  = pathinfo($image_path_full, PATHINFO_DIRNAME);
			if (!file_exists($image_path_dir)) mkdir($image_path_dir, 0777, true);
			if (file_exists($image_path_full)) unlink($image_path_full);
			symlink($path, $image_path_full);
			# spanish
			$es_image = PUB_URL."es/$es_url.$ext";
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
		$es = "$es_cate/$es_name";
		$en = "$en_cate/$en_name";
		return array(
			'es_url'  => $es,
			'en_url'  => $en
		);
	}
}