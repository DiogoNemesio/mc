<?php

/**
 * Parâmetro
 * 
 * @package: MCCondParametro
 * @created: 26/11/2010
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class MCCondParametro {

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
     * Resgata a lista de parâmetros
     *
     * @param integer $codCondominio
     * @return array
     */
    public static function lista ($codCondominio) {
		global $system;
		
    	return (
    		$system->db->extraiPrimeiro("
				SELECT	P.*
				FROM	PARAMETROS_CONDOMINIO P 
				WHERE	P.codCondominio 	= '".$codCondominio."'
			")
   		);
    }


    /**
	 * Salva o valor de um parâmetro
	 * 
	 * @param integer $codCondominio
	 * @param varchar $parametro
	 * @param varchar $valor
	 * @return boolean 
     */
    public static function salva($codCondominio,$dia = null,$skin = null, $planoContaUni = null) {
		global $system;
		
		/** Verifica se já existe o registro de parâmetros **/
		if (MCCondParametro::existe($codCondominio) == true) {
			return (MCCondParametro::update($codCondominio,$dia,$skin, $planoContaUni));
		}else{
			return (MCCondParametro::insert($codCondominio,$dia,$skin, $planoContaUni));
		}
    }
   
    /**
	 * Verifica se existe o registro de parâmetros
	 * 
	 * @param integer $codCondominio
	 * @return boolean 
     */
    public function existe($codCondominio) {
		global $system;
		
    	$info	= $system->db->extraiPrimeiro("
			SELECT	COUNT(*) NUM
			FROM	PARAMETROS_CONDOMINIO P 
			WHERE	P.codCondominio 	= '".$codCondominio."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
    /**
	 * Inserir no banco
	 * 
	 * @param integer $codCondominio
	 * @param integer $dia
	 * @param varchar $skin
	 * @param varchar $planoContaUni
	 * @return boolean 
     */
    public function insert($codCondominio,$dia = null,$skin = null, $planoContaUni = null) {
		global $system;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO PARAMETROS_CONDOMINIO (codCondominio,diaPagamentoSalario,codSkin,codPlanoContaUnidades) VALUES (?,?,?,?)",
				array($codCondominio,$dia,$skin,$planoContaUni)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    
    /**
	 * Update no banco
	 * 
	 * @param integer $codCondominio
	 * @param integer $dia
	 * @param varchar $skin
	 * @param varchar $planoContaUni
	 * @return boolean 
     */
    public function update($codCondominio,$dia = null,$skin = null, $planoContaUni = null) {
		global $system;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("
			UPDATE	PARAMETROS_CONDOMINIO P
			SET		P.diaPagamentoSalario 	= ?,
					P.codSkin				= ?,
					P.codPlanoContaUnidades	= ?
			WHERE 	P.codCondominio 		= ?",
				array($dia,$skin,$planoContaUni,$codCondominio)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Salva o valor de um parâmetro
	 * 
	 * @param integer $codCondominio
	 * @param varchar $parametro
	 * @param varchar $valor
	 * @return boolean 
     */
    public static function salvaParametro($codCondominio,$parametro,$valor) {
		global $system;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("UPDATE PARAMETROS_CONDOMINIO P SET P.".$parametro." = ? WHERE codCondominio = ?",
				array($valor,$codCondominio)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Exclui os parâmetros do condomínio
	 * 
	 * @param integer $codCondominio
	 * @return boolean 
     */
    public static function exclui ($codCondominio) {
		global $system;
		
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("DELETE FROM PARAMETROS_CONDOMINIO WHERE codCondominio = ?",
				array($codCondominio)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }
    
}