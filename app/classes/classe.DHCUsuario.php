<?php

/**
 * Gerenciamento de usuários
 * 
 * @package: DHCUsuario
 * @created: 29/10/2008
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class DHCUsuario {

	/**
	 * código do usuário
	 */
	private $codUsuario;
	
	/**
	 * usuário
	 */
	private $usuario;
	
	/**
	 * Nome
	 */
	private $nome;
	
	/**
	 * Tipo
	 */
	private $tipo;
	
    /**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {
		global $system;
		
		$system->log->debug->debug("DHCUsuario: nova instância");
		
	}
	
	/**
	 * Autenticar o usuário no banco
	 *
	 * @param string $usuario
	 * @param string $senha
	 * @return boolean
	 */
    public function autenticar ($usuario,$senha) {
    	
    	global $system;
    	    	
    	/** Faz a autenticação no banco **/
    	$arr = $system->db->extraiPrimeiro("
    		SELECT 	*
    		FROM 	USUARIOS U
    		WHERE 	U.usuario 	= '".$usuario."'
    		AND 	U.senha 	= '".$senha."'
		");
    	
    	if (isset($arr->codUsuario)) {
    		
    		/** Verifica se o usuário está ativo **/
    		if ($arr->codStatus == 'D') {
    			$system->log->debug->debug('Usuário '.$usuario. ' desativado !!! ');
    			return 2;
    		}
    		
    		/** Atualiza os atributos **/
    		$this->setCodUsuario($arr->codUsuario);
    		$this->setNome($arr->nome);
    		$this->setUsuario($arr->usuario);
			/** Define o tipo do usuário **/
			$infoU	= $this->getInfo($arr->codUsuario);
			$this->setTipo($infoU->codTipo);
    		return true;
		}else{
			return false;
		}
    }
    
    /**
     * Resgatar o código do usuário
     *
     * @return string
     */
    public function getCodUsuario () {
    	return $this->codUsuario;
    }
    
    /**
     * Definir o código do usuário
     *
     * @param string $codUsuario
     */
    public function setCodUsuario ($codUsuario) {
    	$this->codUsuario = $codUsuario;
    }
    
    /**
     * Resgatar a identificação do usuário
     *
     * @return string
     */
    public function getUsuario () {
    	return $this->usuario;
    }
    
    /**
     * Definir a identificação do usuário
     *
     * @param string $usuario
     */
    public function setUsuario ($usuario) {
    	$this->usuario = $usuario;
    }
    
    /**
     * Resgatar o nome do usuário
     *
     * @return string
     */
    public function getNome () {
    	return $this->nome;
    }
    
    /**
     * Definir o nome do usuário
     *
     * @param string $nome
     */
    public function setNome ($nome) {
    	$this->nome = $nome;
    }

    /**
     * Resgatar o tipo do usuário
     *
     * @return string
     */
    public function getTipo () {
    	return $this->tipo;
    }
    
    /**
     * Definir o tipo do usuário
     *
     * @param string $tipo
     */
    public function setTipo ($tipo) {
    	$this->tipo = $tipo;
    }
    
    /**
     * Resgata as informações do usuário
     *
     * @param integer $usuario
     * @return array
     */
    public static function getInfo ($codUsuario) {
		global $system;
			
    	return (
    		$system->db->extraiPrimeiro("
				SELECT	U.*, TU.*
				FROM	USUARIOS U, TIPO_USUARIO TU
				WHERE   TU.codTipo 		= U.codTipo
				AND 	U.codUsuario 	= '".$codUsuario."'

			")
   		);	
    }
    
    
}
