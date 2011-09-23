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
		# run this before render
		$this->view->onrender = function($view){#use ($self){ #  $self is a copy of $this.
			if ($view->ishome) $view->tag_title = "BESTLABS LTD. {$view->subtitle}";
			else $view->tag_title = $view->subtitle." | BESTLABS";
			$view->tag_meta('description', $view->description);
			$view->tag_meta('keywords',    $view->keywords);
		};
		# use custom template (TODO: apply normalize.css instead.)
		$this->view->template   = true;
		# every view is gonna need these.
		$this->view->pages      = $this->model->pages();
		$this->view->categories = $this->model->categories();
		# determine what are we going to display?
		if ($a = $this->model->product($product, $section)) return $this->product($a);
		if ($a = $this->model->section($section)) return $this->section($a);
		# if the last two lines didn't catch a product or session render 404.
		if ($section || $product) $this->reload(URL.'404');
		# main index
		$this->view->tag_jsini(PUB_URL.'jquery.ubillboard.min.js');
 		$this->view->products    = $this->model->products();
		$this->view->description = $this->view->pages['products']['desc'];
		$this->view->keywords    = $this->view->pages['products']['keyw'];
		$this->view->subtitle    = 'Productos farmacéuticos para atletas.';
		$this->view->current     = 'index';
		$this->view->ishome      = true;
	}

	/**
	 * @created 2011/SEP/23 05:40
	 */
	private function section($section){
		$this->view->subtitle    = ucwords($section['name']);
		$this->view->current     = $section['class'];
		$this->view->ishome      = false;
		$this->view->description = $section['desc'];
		$this->view->keywords    = $section['keyw'];
		# is static page
		if (array_pop($section)) return;
		# is category index
		$this->view->tag_jsini(PUB_URL.'jquery.ubillboard.min.js');
		$this->view->products   = $this->model->products($section['class']);
		$this->view->current  = 'index';
	}


	private function product($product){
		print_r($product);
		stop();
	}


	private function common(){
		
	}

}
