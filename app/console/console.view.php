<?php
/**
* Console View
*
* @created 2011/AUG/26 20:36
*/
class consoleView extends View {

	/**
	 * Pseudo constructor
	 *
	 * @created 2011/AUG/26 20:36
	 */
	public function console(){
		#enable authentication library.
		$this->auth = Auth::view($this);
		echo "ssss";
	}

}
