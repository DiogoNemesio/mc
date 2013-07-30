<?php

/**
 * Parâmetro
 * 
 * @package: MCVencCondominio
 * @created: 23/08/2011
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class MCVencCondominio {

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
     * Resgata a lista de vencimentos de um condomínio
     *
     * @param integer $codCondominio
     * @return array
     */
    public static function lista ($codCondominio) {
		global $system;
		
		return ($system->db->extraiTodos ( "
				SELECT	VC.*
				FROM	VENCIMENTOS_CONDOMINIOS VC
				WHERE	VC.codCondominio	= '" . $codCondominio . "'
				ORDER	BY VC.dia
			")
		);
    }


    /**
	 * Salva um vencimento
	 * 
	 * @param integer $codCondominio
	 * @param varchar $parametro
	 * @param varchar $valor
	 * @return boolean 
     */
    public function salva($codCondominio,$dia = null,$skin = null, $planoContaUni = null) {
		global $system;
		
		/** Verifica se já existe o registro de parâmetros **/
		if (MCCondParametro::existe($codCondominio) == true) {
			return (MCCondParametro::update($codCondominio,$dia,$skin, $planoContaUni));
		}else{
			return (MCCondParametro::insert($codCondominio,$dia,$skin, $planoContaUni));
		}
    }
   
    /**
	 * Verifica se o código de vencimento existe
	 * 
	 * @param integer $codVencimento
	 * @return boolean 
     */
    public function existeCodigo($codVencimento) {
		global $system;
		
    	$info	= $system->db->extraiPrimeiro("
			SELECT	COUNT(*) NUM
			FROM	VENCIMENTOS_CONDOMINIOS VC 
			WHERE	P.codVencimento 	= '".$codVencimento."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }

    /**
	 * Verifica se existe o dia de vencimento para determinado condomínio
	 * 
	 * @param integer $codCondominio
	 * @param integer $dia
	 * @return boolean 
     */
    public function existe($codCondominio,$dia) {
		global $system;
		
    	$info	= $system->db->extraiPrimeiro("
			SELECT	COUNT(*) NUM
			FROM	VENCIMENTOS_CONDOMINIOS VC 
			WHERE	VC.codCondominio 	= '".$codCondominio."'
			AND		VC.dia				= '".$dia."'
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
	 * @return boolean 
     */
    public function insert($codCondominio,$dia) {
		global $system;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO VENCIMENTOS_CONDOMINIOS (codCondominio,dia) VALUES (?,?)",
				array($codCondominio,$dia)
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
	 * @return boolean 
     */
    public function update($codVencimento,$codCondominio,$dia) {
		global $system;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("
			UPDATE	VENCIMENTOS_CONDOMINIOS VC
			SET		VC.dia					= ?
			WHERE 	VC.codVencimento		= ?
			AND		VC.codCondominio 		= ?",
				array($dia,$codVencimento,$codCondominio)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    
    /**
	 * Verifica se o vencimento está em uso por alguma unidade
	 * 
	 * @param integer $codVencimento
	 * @return boolean 
     */
    public function estaEmUso($codVencimento) {
		global $system;
		
    	$info	= $system->db->extraiPrimeiro("
			SELECT	COUNT(*) NUM
			FROM	UNIDADES U 
			WHERE	U.codVencimento 	= '".$codVencimento."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
    /**
	 * Exclui um vencimento
	 * 
	 * @param integer $codVencimento
	 * @return boolean 
     */
    public static function exclui ($codVencimento) {
		global $system;
		
		/** Verifica se o vencimento está em uso **/
		if (MCVencCondominio::estaEmUso($codVencimento) == true) {
			return ('Erro: Vencimento em uso por alguma UNIDADE');
		}
		
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("DELETE FROM VENCIMENTOS_CONDOMINIOS WHERE codVencimento = ?",
				array($codVencimento)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }
    
}