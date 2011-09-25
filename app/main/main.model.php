<?php
/**
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
 * @created 2011/SEP/21 12:36
 */
class mainModel extends Model {

	/**
	 * @created 2011/SEP/24 07:12
	 */
	public function alternate($uri){
		$uri = explode('/', $uri);
		$lang = $this->language == 'es'? 'en' : 'es';
		# products
		if (count($uri) == 2)
			$uri[1] = $this->db->select('product','urln','lang=? AND class=? LIMIT 1',$lang, $uri[1]);
		if (count($uri) > 0 && $uri[0]){
			$x = explode('_', $uri[0]);
			$x[0] = $x[0] == 'pages'? 'static' : 'category';
			$uri[0] = '/'.$this->db->select($x[0],'url','lang=? and class=? LIMIT 1',$lang, $x[1]);
		}
		return URL.$lang.implode('/', $uri);
	}


	/**
	 * @created 2011/SEP/21 17:13
	 */
	public function pages(){
		if (isset($this->pages)) return $this->pages;
		# return only specified language if available.
		if (isset($this->language))
			return $this->col2key('class', $this->db->select(
				'static',' class, url, name, keyw, desc', 'lang=?', $this->language
			), true);
		# return both languages
		$all = $this->db->select('static','lang, class, url, name, keyw, desc');
		$new = array();
		foreach ($this->col2key('lang', $all) as $k=>$v)
			$new[$k] = $this->col2key('class', $v, true);
		return $new;
	}

	/**
	 * @created 2011/SEP/22 08:40
	 */
	public function products($category=''){
		$limit = $category? 'lang=? AND categ=?' : 'LANG=? ?';
		# grouped by category
		return $this->col2key('categ',$this->db->select(
			'product',
			'categ, class, name, cont, keyw, desc, urln, urlc, urli',
			$limit, $this->language, $category
		));
	}

	/**
	 * @created 2011/SEP/22 12:33
	 */
	public function categories(){
		return $this->col2key('class', $this->db->select(
			'category',
			'name, url, class',
			'lang=? GROUP BY `class`', $this->language
		), 
		true);
	}

	/**
	 * @created 2011/SEP/22 06:22
	 */
	public function product($urln, $urlc){
		return $this->db->select(
			'product',
			'class, categ, name, cont, keyw, desc, urli',
			'lang=? AND urlc=? AND urln=?', $this->language, $urlc, $urln
		);
	}

	/**
	 * @created 2011/SEP/24 08:46
	 */
	public function product_list(){
		$x = $this->col2key('class', 
			$this->db->select('product','class,name','lang=? ORDER BY `class`', $this->language), true
		);
		array_walk($x, function(&$val){
			$val = $val['name'];
		});
		return $x;
	}


	public function category($name){
		$x = $this->db->select('category','*','lang=? AND class=?', $this->language, $name);
		return array_shift($x);
	}

	/**
	 * @created 2011/SEP/22 08:28
	 */
	public function section($name){
		# is this a static page? 
		$pages = $this->pages();
		$found = false;
		foreach($pages as $pages) {
			if ($pages['url'] != $name) continue;
			$found = true;
			break;
		}
		if ($found)	return array_merge($pages, array('is_page' => true ));
		# is it a category then?
		$category = $this->db->select(
			'category',
			'class, name, url, keyw, desc',
			'lang=? AND url=?', $this->language, $name
		);
		if (!empty($category)) return array_merge($category[0], array('is_page' => false ));
		# is neither.
		return false;
	}

	/**
	 * It's hard to explain, you better use it to understand the cooolness of this.	 
	 * @created 2011/SEP/22 10:56
	 */
	public function col2key($key, $array, $reduce_if_one=false){
		$result = array();
		foreach($array as $k=>$v) {
			if (!isset($v[$key])) error('The specified column does not exist.');
			$name = $v[$key];
			if (isset($result[$name])) $result[$name][] = $v;
			else $result[$name] = array($v);
		}
		if (!$reduce_if_one) return $result;
		# if only one result, no need of subarray.
		foreach($result as $key=>$val) 
			if (count($val) == 1) $result[$key] = $val[0];
		return $result;
	}

	/**
	 * @created 2011/SEP/24 02:47
	 */
	public function htmlify($content){

		$content = preg_split("/[\r\n]{2,}/m", $content);
		array_walk($content, function(&$val){ 
			$val = preg_replace('/[\r\n]/m', '<br>', $val);
			$val = preg_replace('/\*\*([^\*]+)\*\*/m', '<strong>$1</strong>', $val);
			$val = preg_replace('/\*([^\*]+)\*/m', '<em>$1</em>', $val);
			$val = preg_replace('/--([^\-]+)--/m', '<del>$1</del>', $val);
			$val = preg_replace('/__([^_]+)__/m', '<ins>$1</ins>', $val);
			$val = preg_replace('/\[([^\[]+)\]\(([^\)]+)\)/', '<a href="$2">$1</a>',$val);
			$val = "<p>$val</p>";
		});
		return implode('', $content);
	}

	/**
	 * @created 2011/SEP/25 08:27
	 */
	public function authentic(){
		$rx = "/^[a-zA-Z0-9]{16}$/";
		if (
			!isset($_POST['external'])
		||	!isset($_POST['internal'])
		||	!preg_match($rx, $_POST['external'])
		||	!preg_match($rx, $_POST['internal'])
		) return false;
		$ext = $this->db->select('stock','product, expires','id=? AND valided=0', $_POST['external']);
		$int = $this->db->select('stock','product, expires','id=? AND valided=0', $_POST['internal']);
		if (empty($ext) || empty($int) || $ext !== $int) return false;
		# houston we've a match.
		# Mark it as validated and get product info.
		$this->db->update('stock', array('valided' => 1), 'id=?', $_POST['external']);
		$this->db->update('stock', array('valided' => 1), 'id=?', $_POST['internal']);
		return $this->db->select(
			'product',
			'name, urli',
			'class=? AND lang=? LIMIT 1', $ext[0]['product'], $this->language
		);
	}

	/**
	 * @created 2011/SEP/24 23:36
	 */
	public function mail(){
		$self_content = '';
		while(list($key,$val) = @each($_POST)) {
			if (empty($val)) continue;
			$v[$key] = addslashes($val);
			$self_content .= "$key: $val\n";
		}
		if (!isset($v['email']) || !filter_var($v['email'], FILTER_VALIDATE_EMAIL))
			return $this->language == 'es'?	'Correo Inválido' : 'Invalid Mail';
		$email_self = 'info@bestlabsltd.com';#'info@bestlabsltd.com';
		$email_from = 'BESTLABS LTD <info@bestlabsltd.com>';
		$ui = array(
			'es' => array(
				'self_subject' => 'Solicitud de contácto',
				'self_header'  => 'Datos:',
				'self_content' => $self_content,
				'self_footer'  => '',
				'them_subject' => 'Confirmación de contacto en bestlabsltd.com',
				'them_footer'  => '',
				'them_header'  => 'Hola,',
				'them_content' => 'Esta es una confirmación de que su correo ha sido procesado con éxito. Un agente de ventas se comunicará con usted a la brevedad.',
				'them_footer'  => '¡Estamos a sus órdenes!',
			),
			'en' => array(
				'self_subject' => 'Contact request',
				'self_header'  => 'Data:',
				'self_content' => $self_content,
				'self_footer'  => '',
				'them_subject' => 'Contact confirmation in bestlabsltd.com',
				'them_header'  => 'Hi,',
				'them_content' => 'This is a confirmation that our server processed your request succesfully, A sales representative will get in touch with you shortly.',
				'them_footer'  => 'Thanks for contacting us!',
			)
		);
		$ui = $ui[$this->language];
		$self_content = $ui['self_header']."\n\n".$ui['self_content']."\n\n".$ui['self_footer'];
		$them_content = $ui['them_header']."\n\n".$ui['them_content']."\n\n".$ui['them_footer'];
		$them_content = $ui['them_header']."\n\n".$ui['them_content']."\n\n".$ui['them_footer'];
		try {
			mail($email_self, $ui['self_subject'], $self_content, "From: $email_from");
			mail($v['email'], $ui['them_subject'], $them_content, "From: $email_from");
		} catch (Exception $e) {
			Core::error(500);
			echo $this->language == 'es'? 'Error Interno del servidor' : 'Internal server error';
			return true;
		}
		return true;
	}

}
