<?php
/**
* consola View
*
* @created 2011/AUG/26 20:36
*/
class consolaView extends View {

	/**
	 * Pseudo constructor
	 *
	 * @created 2011/AUG/26 20:36
	 */
	public function consola(){
		# Enable view functions.
		Auth::view($this);
	}

}

