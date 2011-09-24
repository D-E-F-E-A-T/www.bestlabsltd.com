<?php
/**
 * @log 2011/AUG/24 21:17 Removed unnecessary code
 */
class mainControl extends Control {

	/**
	 * @created 2011/SEP/21 07:41
	 */
	public function main($lang='', $section='', $product=''){
		# redirect to default language
		if (!$lang) $this->reload('es',true);
		# set language
		$this->language = $this->model->language = $this->view->language = $lang;
		# use custom template (TODO: apply normalize.css instead.)
		$this->view->template = true;
		# run function before view render
		$self = &$this;
		$this->view->onrender =	function($view) use ($self) {
			if ($view->ishome) $view->tag_title = "BESTLABS LTD. {$view->subtitle}";
			else $view->tag_title = $view->subtitle." | BESTLABS";
			$view->tag_meta('description', $self->desc);
			$view->tag_meta('keywords',    $self->keyw);
			# alternate version
			$view->alternate = $self->model->alternate($self->uri);
		};
		# every view is gonna need these.
		$this->view->ishome     = false;
		$this->view->pages      = $this->model->pages();
		$this->view->categories = $this->model->categories();
		# determine what are we going to display?
		if ($a = $this->model->product($product, $section)) return $this->product($a);
		if ($a = $this->model->section($section))           return $this->section($a);
		# if the last two lines didn't catch a product or session render 404.
		if ($section || $product) $this->reload(URL.'404');
		# main index
		$this->view->tag_jsini(PUB_URL.'jquery.ubillboard.min.js');
		$this->desc = $this->view->pages['products']['desc'];
		$this->keyw = $this->view->pages['products']['keyw'];
 		$this->view->products = $this->model->products();
		$this->view->subtitle = 'Productos farmacéuticos para atletas.';
		$this->view->current  = 'index';
		$this->view->ishome   = true;
		$this->uri    = '';
	}

	/**
	 * @created 2011/SEP/23 05:40
	 */
	private function section($section){
		$this->desc = $section['desc'];
		$this->keyw = $section['keyw'];
		$this->uri  = "pages_{$section['class']}";
		$this->view->subtitle    = ucwords($section['name']);
		$this->view->current     = $section['class'];

		# is static page
		if (array_pop($section)) {
			if ($section['class'] == 'contact-us'){
				$this->view->products = $this->model->product_list();
			}
			return;
		}
		# is category index
		$this->uri  = "categ_{$section['class']}";
		$this->view->tag_jsini(PUB_URL.'jquery.ubillboard.min.js');
		$this->view->products   = $this->model->products($section['class']);
		$this->view->current  = 'index';
	}

	/**
	 * @created 2011/SEP/24 01:25
	 */
	private function product($product){
		$product = $product[0];
		$this->desc = $product['desc'];
		$this->keyw = $product['keyw'];
		$this->uri  = "categ_{$product['categ']}/{$product['class']}";
		
		$this->view->subtitle    = ucwords($product['name']);
		$this->view->content     = $this->model->htmlify($product['cont']);
		$this->view->image       = $product['urli'];
		$this->view->current     = 'product';
		# obtain similar products
		$category = $this->model->products($product['categ']);
		$category = $this->model->col2key('class', array_shift($category), true);
		unset($category[$product['class']]);
		$this->view->category = array(
			'info'     => $this->model->category($product['categ']),
			'products' => $category
		);
	}

}
