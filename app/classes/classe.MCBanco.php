<?php

/**
 * Bancos
 * 
 * @package: MCBanco
 * @created: 03/05/2012
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class MCBanco {

	/**
     * Construtor
     *
	 * @return void
	 */
	private function __construct() {
		global $system;

		$system->log->debug->debug(__CLASS__.": nova Instância");
	}
	
	/**
	 * 
	 * Salvar Banco
	 * @param unknown_type $codBanco
	 * @param unknown_type $descricao
	 */
    public static function salva ($codBanco, $descricao) {
		global $system;
		
		/** Checar se o banco já existe **/
		if (MCBanco::existe($codBanco) == false || $codBanco == null) {

			/** Inserir **/
			$err = MCBanco::inserir($codBanco, $descricao);
			if (is_numeric($err)) {
				$codBanco	= $err;
			}else{
				return('Erro: '.$err);
			}
		}else{
			/** Atualizar **/
			return(MCBanco::update($codBanco,$descricao));
		}
    }
	
    /**
     * 
     * Inserir Banco no database
     * @param unknown_type $codBanco
     * @param unknown_type $descricao
     */
    public static function inserir ($codBanco,$descricao) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO BANCOS (codBanco,descricao) VALUES (null,?)",
				array($codBanco,$descricao)
			);
			$cod	= $system->db->con->lastInsertId();
			$system->db->con->commit();
			
			if (!$cod) {
				return('Erro:Não foi possível resgatar o código');
			}else{
				return($cod);
			}
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }

    /**
     * 
     * Atualizar o banco
     * @param unknown_type $codBanco
     * @param unknown_type $descricao
     */
    public static function update ($codBanco, $descricao) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("
				UPDATE 	BANCOS
				SET		descricao		= ?
				WHERE	codBanco		= ?",
				array($descricao,$codBanco)
			);
			$system->db->con->commit();
			return(null);
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }

    /**
     * 
     * Lista os bancos
     */
    public static function lista () {
		global $system;
						
    	return (
    		$system->db->extraiTodos("
				SELECT	*
				FROM	BANCOS
				ORDER	BY descricao
			")
   		);
    }
    
    /**
     * 
     * Verifica se o banco existe
     * @param unknown_type $codBanco
     */
    public static function existe ($codBanco) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT 	COUNT(*) NUM
				FROM 	BANCOS B
				WHERE 	B.codBanco		= '".$codBanco."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    	
    }
    
    /**
     * 
     * Resgata as informações do banco
     * @param unknown_type $codBanco
     */
    public static function getInfo ($codBanco) {
		global $system;
			
    	return (
    		$system->db->extraiPrimeiro("
				SELECT	B.*
				FROM	BANCOS B
				WHERE   codBanco = '".$codBanco."'

			")
   		);	
    }

    /**
     * 
     * Exclui o Banco do Database
     * @param unknown_type $codBanco
     */
	public static function exclui($codBanco) {
		global $system;
		
		/** Verifica se o Banco existe **/
		if (MCBanco::existe($codBanco) == false) return ('Erro: Banco não existe');
		
		/** Verifica se existem informações dependentes **/
		/** contas bancarias e contas bancaria condominio **/
		
		try {
			$system->db->con->beginTransaction ();
			
			/** Apaga o Bloco **/ 
			$system->db->Executa ("DELETE FROM BANCOS WHERE codBanco = ?", array ($codBanco) );
			$system->db->con->commit ();
			return (null);
		} catch ( Exception $e ) {
			$system->db->con->rollback ();
			return ('Erro: ' . $e->getMessage ());
		}
	}
    
}