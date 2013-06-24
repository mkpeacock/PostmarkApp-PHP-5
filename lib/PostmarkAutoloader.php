<?php
class PostmarkAutoloader
{
	public static function Register(){
		return spl_autoload_register(
			array('PostmarkAutoloader', 'Load')
		);
	}
	
	public static function Load($object){
		if('Postmark' !== substr($object, 0, 8)){
			return false;
		}
		require_once sprintf(
			'%s' . DIRECTORY_SEPARATOR . '%s.php',
			dirname(__FILE__),
			$object
		);
	}
}