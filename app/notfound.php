<?php
/**
 * @author Hector Menendez <h@cun.mx>
 * @licence http://etor.mx/licence.txt
 * @created 2011/SEP/23 09:05
 */
class notfoundControl extends Control{
	
	public function notfound(){
		stop("<h1>No existe</h1>");
	}

}