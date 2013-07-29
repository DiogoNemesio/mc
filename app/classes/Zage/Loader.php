<?php

namespace Zage;

/**
 * Carregador de classes
 *
 * @package \Zage\Loader
 * @created 10/07/2013
 * @author Daniel Henrique Cassela
 * @version 1.0
 *         
 */
class Loader {
	
	/**
	 * Carregar automaticamente a classe
	 * 
	 * @param string $class        	
	 * @return void
	 */
	public static function autoload($class) {
		if (defined ( 'CLASS_PATH' ))
			$prodDirs [] = CLASS_PATH;
		
		if (stripos ( $class, '\\' ) === false) {
			for($i = 0; $i < sizeof ( $prodDirs ); $i ++) {
				$file = $prodDirs [$i] . $class . '.php';
				if (file_exists ( $file ))
					include_once ($file);
			}
		} else {
			include_once (CLASS_PATH . DIRECTORY_SEPARATOR . str_replace ( '\\', DIRECTORY_SEPARATOR, $class ) . '.php');
		}
		return false;
	}
}