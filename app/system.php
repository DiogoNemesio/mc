<?php
if ( (!isset($system)) || (!is_object($system)) || $system->estaIniciado() !== true ) {
	/** 
	 * Instancia o sistema 
	 **/
	$system = \MegaCondominio::getInstance ();
	
	/** 
	 * Inicializa o sistema 
	 **/
	$system->inicializaSistema();

}else{
	/**
	 * Inicia os recursos (DB, LOG)
	 */
	$system->iniciaRecursos();
}


if ($_SERVER['DOCUMENT_ROOT']) {
	/** descarregar o buffer de saída **/
	//ob_end_flush();
	/** Checa se a autenticação foi feita **/
	//include_once(BIN_PATH . 'auth.php');
}
