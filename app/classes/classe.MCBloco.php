<?php

/**
 * Condomínio
 * 
 * @package: MCBloco
 * @created: 15/10/2010
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class MCBloco {

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
     * Salvar condominío
     *
     * @param integer $usuario
     * @return array
     */
    public static function salva ($codBloco, $codCondominio,$nomeBloco, $descricao, $codSindico) {
		global $system;
		
		/** Checar se bloco já existe **/
		if (MCBloco::existe($codCondominio,$nomeBloco) == false && $codBloco == null) {

			/** Inserir **/
			$err = MCBloco::inserir($codCondominio,$nomeBloco, $descricao, $codSindico);
			if (is_numeric($err)) {
				$codCondominio	= $err;
			}else{
				return('Erro: '.$err);
			}
		}else{
			/** Atualizar **/
			return(MCBloco::update($codBloco,$nomeBloco, $descricao, $codSindico));
		}
    }
	
	/**
     * Inserir o condomínio no banco
     *
     * @param integer $usuario
     * @return array
     */
    public static function inserir ($codCondominio,$nomeBloco, $descricao, $codSindico = null) {
		global $system;
		
		//$system->log->debug->debug("CodSindico = \"$codSindico\"");
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO BLOCOS (codBloco,codCondominio,nomeBloco,descricao,codSindico) VALUES (null,?,?,?,?)",
				array($codCondominio,$nomeBloco,$descricao,$codSindico)
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
     * Atualizar o condomínio no banco
     *
     * @param integer $usuario
     * @return array
     */
    public static function update ($codBloco,$nomeBloco, $descricao, $codSindico) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("
				UPDATE BLOCOS
				SET		nomeBloco		= ?,
						descricao		= ?,
						codSindico		= ?
				WHERE	codBloco		= ?",
				array($nomeBloco,$descricao,$codSindico,$codBloco)
			);
			$system->db->con->commit();
			return(null);
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }

    /**
     * Lista os blocos do condominio
     *
     * @param integer $usuario
     * @return array
     */
    public static function lista ($codCondominio) {
		global $system;
						
    	return (
    		$system->db->extraiTodos("
				SELECT	B.*,C.*,U.nome nomeSindico
				FROM	BLOCOS 		B 
						LEFT JOIN
						USUARIOS 	U 		ON (B.codSindico 	= U.codUsuario)
						LEFT JOIN		
						CONDOMINIOS C		ON (C.codCondominio = B.codCondominio)
				WHERE	C.codCondominio 	= '".$codCondominio."'
				ORDER	BY nomeBloco
			")
   		);
    }
    
    /**
     * Verifica se o condomínio existe
     *
     * @param integer $usuario
     * @return array
     */
    public static function existe ($codCondominio, $nomeBloco) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT COUNT(*) NUM
				FROM 	BLOCOS B, CONDOMINIOS C
				WHERE 	B.codCondominio = C.codCondominio
				AND 	C.codCondominio = '".$codCondominio."'
				AND		B.nomeBloco		= '".$nomeBloco."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    	
    }
    
    /**
     * Verifica se o condomínio existe
     *
     * @param integer $usuario
     * @return array
     */
    public static function existeCodigo ($codBloco) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT COUNT(*) NUM
				FROM 	BLOCOS B
				WHERE 	B.codBloco	= '".$codBloco."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    	
    }
    
    /**
	 * Lista as unidades do Bloco
	 *
	 * @param integer $codBloco
	 * @return array
	 */
	public static function listaUnidades($codBloco) {
		global $system;
		
		return ($system->db->extraiTodos ( "
				SELECT	U.*
				FROM	UNIDADES 	U
				WHERE	U.codBloco	= '".$codBloco."'
				ORDER	BY U.codUnidade
			" ));
	}
    

    /**
     * Resgata as informações do condomínio
     *
     * @param integer $usuario
     * @return array
     */
    public static function getInfo ($codBloco) {
		global $system;
			
    	return (
    		$system->db->extraiPrimeiro("
				SELECT	B.*
				FROM	BLOCOS B
				WHERE   codBloco = '".$codBloco."'

			")
   		);	
    }

	/**
	 * Exclui o Bloco do banco
	 *
	 * @param integer $codBloco
	 * @return array
	 */
	public static function exclui($codBloco) {
		global $system;
		
		/** Verifica se o Bloco existe **/
		if (MCBloco::existeCodigo($codBloco) == false) return ('Erro: Bloco não existe');
		
		/** Verifica se existem unidades cadastrada **/
		$unidades	= MCBloco::listaUnidades($codBloco);
		if (sizeof($unidades) > 0) return ('Erro: Existem Unidades nesse Bloco');
		
		try {
			$system->db->con->beginTransaction ();
			
			/** Apaga o Bloco **/ 
			$system->db->Executa ("DELETE FROM BLOCOS WHERE codBloco = ?", array ($codBloco) );
			$system->db->con->commit ();
			return (null);
		} catch ( Exception $e ) {
			$system->db->con->rollback ();
			return ('Erro: ' . $e->getMessage ());
		}
	}
    
}