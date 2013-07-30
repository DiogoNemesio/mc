<?php

/**
 * Morador
 * 
 * @package: MCMorador
 * @created: 18/10/2011
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class MCMorador {

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
	 * Salvar um Morador
	 * @param unknown_type $codMorador
	 * @param unknown_type $codUnidade
	 * @param unknown_type $nome
	 * @param unknown_type $fone
	 * @param unknown_type $codTipoSexo
	 * @param unknown_type $codUsuario
	 */
    public static function salva ($codMorador,$codUnidade,$nome,$fone,$codTipoSexo,$codUsuario) {
		global $system;
		
		/** Checar se o morador já existe **/
		if ((!$codMorador) || (MCMorador::existe($codMorador) == false) ) {

			/** Inserir **/
			$err = MCMorador::inserir($codUnidade,$nome,$fone,$codTipoSexo,$codUsuario);
			if (is_numeric($err)) {
				$codMorador	= $err;
			}else{
				return('Erro: '.$err);
			}
		}else{
			/** Atualizar **/
			return(MCMorador::update($codMorador,$codUnidade,$nome,$fone,$codTipoSexo,$codUsuario));
		}
    }
	
    /**
     * 
     * Inserir o Morador no banco
     * @param unknown_type $codUnidade
     * @param unknown_type $nome
     * @param unknown_type $fone
     * @param unknown_type $codTipoSexo
     * @param unknown_type $codUsuario
     */
    public static function inserir ($codUnidade,$nome,$fone,$codTipoSexo,$codUsuario) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO MORADORES (codMorador,codUnidade,nome,fone,codTipoSexo,codUsuario) VALUES (null,?,?,?,?,?)",
				array($codUnidade,$nome,$fone,$codTipoSexo,$codUsuario)
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
     * Atualizar o Morador no banco
     * @param unknown_type $codMorador
     * @param unknown_type $codUnidade
     * @param unknown_type $nome
     * @param unknown_type $fone
     * @param unknown_type $codTipoSexo
     * @param unknown_type $codUsuario
     */
    public static function update ($codMorador,$codUnidade,$nome,$fone,$codTipoSexo,$codUsuario) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("
				UPDATE 	MORADORES 
				SET		codUnidade	= ?,
						nome		= ?,
						fone		= ?,
						codTipoSexo	= ?,
						codUsuario	= ?
				WHERE	codMorador	= ?",
				array($codUnidade,$nome,$fone,$codTipoSexo,$codUsuario,$codMorador)
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
	 * Lista os moradores do condomínio
	 * @param number $codCondominio
	 */
    public static function lista ($codCondominio) {
		global $system;
			
    	return (
    		$system->db->extraiTodos("
				SELECT	M.*,U.nome descUnidade,TS.descricao descSexo
				FROM	UNIDADES			U,
						BLOCOS				B,
						MORADORES			M,
						TIPO_SEXO			TS
				WHERE	M.codUnidade	= U.codUnidade
				AND		U.codBloco		= B.codBloco
				AND		M.codTipoSexo	= TS.codTipo
				AND		B.codCondominio	= '".$codCondominio."'
				ORDER	BY U.nome,M.nome
			")
   		);
    }
        
	/**
	 * 
	 * Lista os moradores por Unidade
	 * @param number $codUnidade
	 */
    public static function listaPorUnidade ($codUnidade) {
		global $system;
						
    	return (
    		$system->db->extraiTodos("
				SELECT	M.*,U.nome descUnidade,TS.descricao descSexo
    			FROM	UNIDADES			U,
						MORADORES			M,
						TIPO_SEXO			TS
						WHERE	M.codUnidade	= U.codUnidade
				AND		M.codTipoSexo	= TS.codTipo
				AND		B.codCondominio	= '".$codUnidade."'
				ORDER	BY U.nome,M.nome
    		")
   		);
    }
    
    /**
     * Verifica se o Morador existe
     *
     * @param integer $codMorador
     * @return array
     */
    public static function existe ($codMorador) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	MORADORES M
				WHERE 	M.codMorador	= '".$codMorador."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }

   
    /**
     * Resgata as informações do Morador
     *
     * @param integer $codMorador
     * @return array
     */
    public static function getInfo ($codMorador) {
		global $system;
			
    	return (
    		$system->db->extraiPrimeiro("
				SELECT	M.*
				FROM	MORADORES M
				WHERE   M.codMorador = '".$codMorador."'
			")
   		);	
    }

    /**
     * Lista os tipos de Sexo
     *
     * @return array
     */
    public static function listaSexos () {
		global $system;
						
    	return (
    		$system->db->extraiTodos("
				SELECT	TS.*
				FROM	TIPO_SEXO TS
				ORDER	BY TS.descricao
			")
   		);
    }
    
    
	/**
	 * Exclui um Morador
	 *
	 * @param integer $codMorador
	 * @return array
	 */
	public static function exclui($codMorador) {
		global $system;
		
		/** Verifica se o Morador existe **/
		if (MCMorador::existe($codMorador) == false) return ('Erro: Morador não existe');
		
		try {
			$system->db->con->beginTransaction ();
			
			/** Apaga o Morador **/ 
			$system->db->Executa ("DELETE FROM MORADORES WHERE codMorador = ?", array ($codMorador) );
			$system->db->con->commit ();
			return (null);
		} catch ( Exception $e ) {
			$system->db->con->rollback ();
			return ('Erro: ' . $e->getMessage ());
		}
	}
}