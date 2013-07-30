<?php

/**
 * Unidade
 * 
 * @package: MCUnidade
 * @created: 26/03/2011
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class MCUnidade {

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
     * Salvar uma unidade
     *
     * @return array
     */
    public static function salva ($codUnidade,$codBloco,$nome,$codResponsavel,$fone,$celular,$codTipo,$codVencimento,$ramal) {
		global $system;
		
		if ((!$codUnidade) && (MCUnidade::existeNome($codBloco,$nome) == true)) {
			$info		= MCUnidade::getInfo(null,$codBloco,$nome);
			$codUnidade	= $info->codUnidade;
		}
		
		/** Checar se a unidade já existe **/
		if ((!$codUnidade) || (MCUnidade::existe($codUnidade) == false) ) {

			/** Inserir **/
			$err = MCUnidade::inserir($codBloco,$nome,$codResponsavel,$fone,$celular,$codTipo,$codVencimento,$ramal);
			if (is_numeric($err)) {
				$codUnidade	= $err;
			}else{
				return('Erro: '.$err);
			}
		}else{
			/** Atualizar **/
			return(MCUnidade::update($codUnidade,$codBloco,$nome,$codResponsavel,$fone,$celular,$codTipo,$codVencimento,$ramal));
		}
    }
	
    /**
     * 
     * Inserir a unidade no banco
     * @param number $codBloco
     * @param string $nome
     * @param number $codResponsavel
     * @param number $fone
     * @param number $celular
     * @param number $codTipo
     * @param number $codVencimento
     * @param number $ramal
     */
    public static function inserir ($codBloco,$nome,$codResponsavel,$fone,$celular,$codTipo,$codVencimento,$ramal) {
		global $system;
		
		if (!$codVencimento)	$codVencimento	= null;
		if (!$codResponsavel) 	$codResponsavel	= null;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO UNIDADES (codUnidade,codBloco,nome,codResponsavel,fone,celular,codTipo,codVencimento,ramal) VALUES (null,?,?,?,?,?,?,?,?)",
				array($codBloco,$nome,$codResponsavel,$fone,$celular,$codTipo,$codVencimento,$ramal)
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
     * Atualizar a unidade no banco
     * @param number $codUnidade
     * @param number $codBloco
     * @param string $nome
     * @param number $codResponsavel
     * @param number $fone
     * @param number $celular
     * @param number $codTipo
     * @param number $codVencimento
     * @param number $ramal
     */
    public static function update ($codUnidade,$codBloco,$nome,$codResponsavel,$fone,$celular,$codTipo,$codVencimento,$ramal) {
		global $system;
		
		if (!$codVencimento)	$codVencimento	= null;
		if (!$codResponsavel) 	$codResponsavel	= null;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("
				UPDATE 	UNIDADES 
				SET		codBloco		= ?,
						nome			= ?,
						codResponsavel	= ?,
						fone			= ?,
						celular			= ?,
						codTipo			= ?,
						codVencimento	= ?,
						ramal			= ?
				WHERE	codUnidade		= ?",
				array($codBloco,$nome,$codResponsavel,$fone,$celular,$codTipo,$codVencimento,$ramal,$codUnidade)
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
	 * Lista as unidades do condomínio
	 * @param number $codBloco
	 */
    public static function lista ($codCondominio) {
		global $system;
			
    	return (
    		$system->db->extraiTodos("
				SELECT	B.*,U.*,TU.descricao tipoUnidade,US.nome nomeResponsavel
				FROM	BLOCOS			B,
						TIPO_UNIDADE	TU,
						UNIDADES		U
						LEFT OUTER JOIN USUARIOS AS US ON U.codResponsavel = US.codUsuario
				WHERE	B.codBloco		= U.codBloco
				AND		U.codTipo		= TU.codTipo
				AND		B.codCondominio	= '".$codCondominio."'
				ORDER	BY U.nome
			")
   		);
    }
        
	/**
	 * 
	 * Lista as unidades do bloco
	 * @param number $codBloco
	 */
    public static function listaPorBloco ($codBloco) {
		global $system;
						
    	return (
    		$system->db->extraiTodos("
				SELECT	B.*,U.*,TU.descricao tipoUnidade
				FROM	BLOCOS 			B,
						UNIDADES 		U,
						TIPO_UNIDADE	TU
				WHERE	B.codBloco		= U.codBloco
				AND		U.codTipo		= TU.codTipo
				and		B.codBloco		= '".$codBloco."'
				ORDER	BY U.codUnidade
			")
   		);
    }
    
    /**
     * Verifica se a Unidade existe
     *
     * @param integer $codUnidade
     * @return array
     */
    public static function existe ($codUnidade) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	UNIDADES U
				WHERE 	U.codUnidade	= '".$codUnidade."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }

    /**
     * Verifica se a Unidade existe
     *
     * @param integer $codUnidade
     * @return array
     */
    public static function existeNome ($codBloco,$nome) {
		global $system;
		$system->log->debug->debug("Bloco: $codBloco, Nome: $nome");
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	UNIDADES U
				WHERE 	U.codBloco	= '".$codBloco."'
				AND		U.nome		= '".$nome."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
    /**
     * Resgata as informações da Unidade
     *
     * @param integer $codUnidade
     * @return array
     */
    public static function getInfo ($codUnidade = null,$codBloco = null,$nome = null) {
		global $system;
			$and		= '';
			if ($codUnidade != null)	$and.= "AND 	codUnidade 	= '".$codUnidade."'"; 
			if ($codBloco 	!= null) 	$and.= "AND 	codBloco	= '".$codBloco."'"; 
			if ($nome 		!= null) 	$and.= "AND 	nome 		= '".$nome."'"; 
			
			if (($codUnidade == null) && ($codBloco == null || $nome == null)) {
				DHCErro::halt(__CLASS__ .': Erro falta de parâmetros');
			}
			
    	return (
    		$system->db->extraiPrimeiro("
				SELECT	U.*
				FROM	UNIDADES U
				WHERE   1 = 1
				$and
			")
   		);	
    }

    /**
     * Lista os tipos de Unidades
     *
     * @return array
     */
    public static function listaTipos () {
		global $system;
						
    	return (
    		$system->db->extraiTodos("
				SELECT	TU.*
				FROM	TIPO_UNIDADE TU
				ORDER	BY TU.descricao
			")
   		);
    }
    
	/**
	 * Exclui a unidade do banco
	 *
	 * @param integer $codUnidade
	 * @return array
	 */
	public static function exclui($codUnidade) {
		global $system;
		
		/** Verifica se o Bloco existe **/
		if (MCUnidade::existe($codUnidade) == false) return ('Erro: Unidade não existe');
		
		try {
			$system->db->con->beginTransaction ();
			
			/** Apaga o Bloco **/ 
			$system->db->Executa ("DELETE FROM UNIDADES WHERE codUnidade = ?", array ($codUnidade) );
			$system->db->con->commit ();
			return (null);
		} catch ( Exception $e ) {
			$system->db->con->rollback ();
			return ('Erro: ' . $e->getMessage ());
		}
	}
}