<?php

/**
 * SysApp - Gerador de Códigos 
 * 
 * @package: sysApp
 * @created: 07/05/2012
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class sysApp {
	
	
	/**
     * Construtor
	 * @return void
	 */
	public function __construct() {
		global $system;

		$system->log->debug->debug(__CLASS__.": nova Instância");
	}
	
	
	/**
	 * 
	 * @param number $codMenu
	 */
	public function load ($codMenu) {
		
	}
	
	/**
	 *  
	 * @param number $codMenu
	 */
	public function genCode ($codMenu) {
		
	}
	
	/**
	 * 
	 * Lista as aplicações existentes
	 */
	public function lista () {
    	global $system; 
    	return($system->db->extraiTodos("
			SELECT	A.*,TA.descricao TIPO_APP
			FROM	MENU_APP A,
					TIPO_APP TA
			WHERE	A.codTipo			= TA.codTipo
			ORDER	BY A.descricao
			"));
			}
		
	
}
