<?php

/**
 * Plano de Contas dos Condomínios
 * 
 * @package: MCPlanoContasCondominio
 * @created: 03/09/2011
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class MCPlanoContasCondominio {

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
     * Resgata a lista de Plano de Contas do condomínio
     *
     * @param integer $codCondominio
     * @return array
     */
    public static function lista ($codCondominio) {
		global $system;
		
    	return (
    		$system->db->extraiTodos("
				SELECT	P.*
				FROM	PLANO_CONTAS_CONDOMINIO P 
				WHERE	P.codCondominio 	= '".$codCondominio."'
			")
   		);
    }

    /**
	 * Verifica se existe o registro de plano de contas por código
	 * 
	 * @param integer $codCondominio
	 * @param integer $codConta
	 * @return boolean 
     */
    public function existe($codCondominio,$codConta = null,$conta = null) {
		global $system;
		
		$and		= '';
		if ($codConta 	!= null)	$and	.= "AND		P.codPlanoConta	= '".$codConta."'";  
		if ($onta 		!= null)	$and	.= "AND		P.conta			= '".$conta."'";

		if (($codConta	== null) && ($conta == null)) DHCErro::halt(__CLASS__.': Falta de Parâmetros');
		
    	$info	= $system->db->extraiPrimeiro("
			SELECT	COUNT(*) NUM
			FROM	PLANO_CONTAS_CONDOMINIO P 
			WHERE	P.codCondominio 	= '".$codCondominio."'
			$and	
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    

    /**
	 * Exclui os parâmetros do condomínio
	 * 
	 * @param integer $codCondominio
	 * @param integer $codConta
	 * @return boolean 
     */
    public static function exclui ($codCondominio,$codConta) {
		global $system;
		
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("DELETE FROM PLANO_CONTAS_CONDOMINIO WHERE codCondominio = ? AND codPlanoConta = ?",
				array($codCondominio,$codConta)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }
    
}