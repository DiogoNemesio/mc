<?php

/**
 * Espaco
 * 
 * @package: MCEspaco
 * @created: 27/12/2011
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class MCEspaco {

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
     * Salvar espaco
     *
     * @param integer $usuario
     * @return array
     */
    public static function salva ($codEspaco, $codCondominio, $nome, $descricao, $tempoMaximo, $indConfirmacao, $valor) {
		global $system;
		
		/** Checar se o espaço já existe **/
		if (MCEspaco::existe($codCondominio,$nome) == false && $codEspaco == null) {

			/** Inserir **/
			$err = MCEspaco::inserir($codCondominio, $nome, $descricao, $tempoMaximo, $indConfirmacao, $valor);
			if (is_numeric($err)) {
				$codCondominio	= $err;
			}else{
				return('Erro: '.$err);
			}
		}else{
			/** Atualizar **/
			return(MCEspaco::update($codEspaco, $codCondominio, $nome, $descricao, $tempoMaximo, $indConfirmacao, $valor));
		}
    }
	
	/**
     * Inserir o espaço no banco
     *
     * @param integer $usuario
     * @return array
     */
    public static function inserir ($codCondominio, $nome, $descricao, $tempoMaximo, $indConfirmacao, $valor) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO ESPACOS (codEspaco,codCondominio,nome,descricao,tempoMaximo,indConfirmacao,valor) VALUES (null,?,?,?,?,?,?)",
				array($codCondominio,$nome,$descricao,$tempoMaximo,$indConfirmacao,$valor)
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
     * Atualizar o espaço no banco
     *
     * @param integer $usuario
     * @return array
     */
    public static function update ($codEspaco, $codCondominio, $nome, $descricao, $tempoMaximo, $indConfirmacao, $valor) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("
				UPDATE 	ESPACOS
				SET		nome			= ?,
						descricao		= ?,
						tempoMaximo		= ?,
						indConfirmacao	= ?,
						valor			= ?
				WHERE	codEspaco		= ?",
				array($nome,$descricao,$tempoMaximo,$indConfirmacao,$valor,$codEspaco)
			);
			$system->db->con->commit();
			return(null);
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }

    /**
     * Lista os espacos do condominio
     *
     * @param integer $usuario
     * @return array
     */
    public static function lista ($codCondominio) {
		global $system;
						
    	return (
    		$system->db->extraiTodos("
				SELECT	E.*,C.*
				FROM	ESPACOS		E,
						CONDOMINIOS C
				WHERE	C.codCondominio 	= '".$codCondominio."'
				AND		E.codCondominio		= C.codCondominio
				ORDER	BY E.nome
			")
   		);
    }
    
    /**
     * Verifica se o espaço existe
     *
     * @param integer $usuario
     * @return array
     */
    public static function existe ($codCondominio, $nome) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT COUNT(*) NUM
				FROM 	ESPACOS E, CONDOMINIOS C
				WHERE 	E.codCondominio = C.codCondominio
				AND 	C.codCondominio = '".$codCondominio."'
				AND		E.nome			= '".$nome."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    	
    }
    
    /**
     * Verifica se o espaço existe
     *
     * @param integer $usuario
     * @return array
     */
    public static function existeCodigo ($codEspaco) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM 	ESPACOS E
				WHERE 	E.codEspaco	= '".$codEspaco."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    	
    }
    

    /**
     * Resgata as informações do espaço
     *
     * @param integer $usuario
     * @return array
     */
    public static function getInfo ($codEspaco) {
		global $system;
			
    	return (
    		$system->db->extraiPrimeiro("
				SELECT	E.*
				FROM	ESPACOS E
				WHERE   codEspaco = '".$codEspaco."'

			")
   		);	
    }

	/**
	 * Exclui o Espaço do banco
	 *
	 * @param integer $codEspaco
	 * @return array
	 */
	public static function exclui($codEspaco) {
		global $system;
		
		/** Verifica se o Espaço existe **/
		if (MCEspaco::existeCodigo($codEspaco) == false) return ('Erro: Espaço não existe');
		
	
		try {
			$system->db->con->beginTransaction ();
			
			/** Apaga o Bloco **/ 
			$system->db->Executa ("DELETE FROM ESPACOS WHERE codEspaco = ?", array ($codEspaco) );
			$system->db->con->commit ();
			return (null);
		} catch ( Exception $e ) {
			$system->db->con->rollback ();
			return ('Erro: ' . $e->getMessage ());
		}
	}
    
}