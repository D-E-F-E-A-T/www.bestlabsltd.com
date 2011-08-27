<?php
/**
 * @created 2011/AUG/27 02:39
 */
final class viewAuth extends Library {

	/**
	 * Outputs Login form.
	 *
	 * @created 2011/AUG/2011 03:55
	 */
	public function render(){
		if (!file_exists($_PATH = AUTH.'auth.html'))
			error('Auth HTML is missing');
		ob_end_clean();
		ob_start();
		include $_PATH;
		return ob_get_clean();
	}

}