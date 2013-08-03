<?php

/**
 * Usuário
 *
 * @package Usuario
 * @author Daniel Henrique Cassela
 * @version 1.0.1
 */
class Usuario extends \Zage\Usuario {

    /**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $log;
		
		$log->debug(__CLASS__.": nova instância");
		
	}
	
	/**
	 * Autenticar o usuário no banco
	 *
	 * @param string $usuario
	 * @param string $senha
	 * @return boolean
	 */
    public final function autenticar ($usuario,$senha) {
    	global $db,$log;
    	
    	/** Faz a autenticação no banco **/
    	$arr = $db->extraiPrimeiro("
                SELECT  *
                FROM    USUARIOS U
                WHERE   U.usuario       = '".$usuario."'
                AND     U.senha         = '".$senha."'
                ");
    	
    	if (isset($arr->codUsuario)) {
    	
    		/** Verifica se o usuário está ativo **/
    		if ($arr->codStatus == 'D') {
    			$log->debug('Usuário '.$usuario. ' desativado !!! ');
    			return 2;
    		}
    	
    		/** Atualiza os atributos **/
    		$this->setCodUsuario($arr->codUsuario);
    		$this->setNome($arr->nome);
    		$this->setUsuario($arr->usuario);
    		
    		/** Define o tipo do usuário **/
    		$infoU  = $this->getInfo($arr->codUsuario);
    		$this->setTipo($infoU->codTipo);
    		return true;
    	}else{
    		return false;
    	}
    	 
    }
    
    /**
     * Resgata as informações do usuário
     *
     * @param integer $usuario
     * @return array
     */
    public final function getInfo ($codUsuario) {
    	global $db;
    	return ($db->extraiPrimeiro("
			SELECT  U.*, TU.*
			FROM    USUARIOS U, TIPO_USUARIO TU
			WHERE   TU.codTipo		= U.codTipo
			AND     U.codUsuario    = '".$codUsuario."'
		"));
    }
    
}
