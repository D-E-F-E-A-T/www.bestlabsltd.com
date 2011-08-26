<?php
/**
* Main Controller
* 
* @log Created 2011/AGO/23 22:41
*/
class mainControl extends Control{

	public function main(){
		#stop($this);

		$db = DB::sqlite('memory');
		stop($db);
	}

}