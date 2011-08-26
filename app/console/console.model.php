<?php
/**
* Console Model
*
* @created 2011/AUG/23 23:18
*/
class consoleModel extends Model{
	

	/**
	 * Pseudo constructor
	 *
	 * @created 2011/AUG/24 00:32
	 */
	public function console(){
		#enable authentication library.
		$this->auth = Auth::load($this);
	}

}

