<?php
/**
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
 * @created 2011/SEP/21 12:36
 */
class mainModel extends Model {

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
		$all = $this->db->select('static','lang,class, url, name, keyw, desc');
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
	public function product($name, $category){
		return $this->db->select(
			'product',
			'class, name, cont, keyw, desc, urli',
			'lang=? AND urlc=? AND urln=?', $this->language, $category, $name
		);
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
	 * @author Hector Menendez <h@cun.mx>
	 * @licence http://etor.mx/licence.txt
	 * @created 2011/SEP/22 10:56
	 */
	private function col2key($key, $array, $reduce_if_one=false){
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

}
