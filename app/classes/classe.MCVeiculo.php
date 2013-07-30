<?php

/**
 * Veículo
 * 
 * @package: MCVeiculo
 * @created: 17/10/2011
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class MCVeiculo {

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
	 * Salvar um Veículo
	 * @param unknown_type $codVeiculo
	 * @param unknown_type $codUnidade
	 * @param unknown_type $codMarca
	 * @param unknown_type $modelo
	 * @param unknown_type $cor
	 * @param unknown_type $placa
	 */
    public static function salva ($codVeiculo,$codUnidade,$codMarca,$modelo,$cor,$placa) {
		global $system;
		
		/** Checar se o veículo já existe **/
		if ((!$codVeiculo) || (MCVeiculo::existe($codVeiculo) == false) ) {

			/** Inserir **/
			$err = MCVeiculo::inserir($codUnidade,$codMarca,$modelo,$cor,$placa);
			if (is_numeric($err)) {
				$codVeiculo	= $err;
			}else{
				return('Erro: '.$err);
			}
		}else{
			/** Atualizar **/
			return(MCVeiculo::update($codVeiculo,$codUnidade,$codMarca,$modelo,$cor,$placa));
		}
    }
	
    /**
     * 
     * Inserir o Veículo no banco
     * @param unknown_type $codUnidade
     * @param unknown_type $codMarca
     * @param unknown_type $modelo
     * @param unknown_type $cor
     * @param unknown_type $placa
     */
    public static function inserir ($codUnidade,$codMarca,$modelo,$cor,$placa) {
		global $system;
		
		if ((!$codMarca) || (strtoupper($codMarca) == 'NULL'))			$codMarca	= null;

		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO VEICULOS (codVeiculo,codUnidade,codMarca,modelo,cor,placa) VALUES (null,?,?,?,?,?)",
				array($codUnidade,$codMarca,$modelo,$cor,$placa)
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
     * Atualizar o veículo no banco
     * @param unknown_type $codVeiculo
     * @param unknown_type $codUnidade
     * @param unknown_type $codMarca
     * @param unknown_type $modelo
     * @param unknown_type $cor
     * @param unknown_type $placa
     */
    public static function update ($codVeiculo,$codUnidade,$codMarca,$modelo,$cor,$placa) {
		global $system;
		
		if ((!$codMarca) || (strtoupper($codMarca) == 'NULL'))			$codMarca	= null;
				
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("
				UPDATE 	VEICULOS 
				SET		codUnidade	= ?,
						codMarca	= ?,
						modelo		= ?,
						cor			= ?,
						placa		= ?
				WHERE	codVeiculo	= ?",
				array($codUnidade,$codMarca,$modelo,$cor,$placa,$codVeiculo)
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
	 * Lista os veículos do condomínio
	 * @param number $codCondominio
	 */
    public static function lista ($codCondominio) {
		global $system;
			
    	return (
    		$system->db->extraiTodos("
				SELECT	V.*,U.*,TM.descricao descMarca
				FROM	UNIDADES			U,
						BLOCOS				B,
						VEICULOS			V,
						TIPO_MARCA_VEICULO	TM
				WHERE	V.codUnidade	= U.codUnidade
				AND		U.codBloco		= B.codBloco
				AND		V.codMarca		= TM.codMarca
				AND		B.codCondominio	= '".$codCondominio."'
				ORDER	BY U.nome
			")
   		);
    }
        
	/**
	 * 
	 * Lista os veículos por Unidade
	 * @param number $codUnidade
	 */
    public static function listaPorUnidade ($codUnidade) {
		global $system;
						
    	return (
    		$system->db->extraiTodos("
				SELECT	V.*,U.*,TM.descricao descMarca
				FROM	UNIDADES			U,
						VEICULOS			V,
						TIPO_MARCA_VEICULO	TM
				WHERE	V.codUnidade	= U.codUnidade
				AND		V.codMarca		= TM.codMarca
				AND		U.codUnidade	= '".$codUnidade."'
				ORDER	BY U.nome
			")
   		);
    }
    
    /**
     * Verifica se o Veículo existe
     *
     * @param integer $codVeiculo
     * @return array
     */
    public static function existe ($codVeiculo) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM
				FROM	VEICULOS V
				WHERE 	V.codVeiculo	= '".$codVeiculo."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }

   
    /**
     * Resgata as informações do Veículo
     *
     * @param integer $codVeiculo
     * @return array
     */
    public static function getInfo ($codVeiculo) {
		global $system;
			
    	return (
    		$system->db->extraiPrimeiro("
				SELECT	V.*
				FROM	VEICULOS V
				WHERE   V.codVeiculo = '".$codVeiculo."'
			")
   		);	
    }

    /**
     * Lista as marcas de Veículos
     *
     * @return array
     */
    public static function listaMarcas () {
		global $system;
						
    	return (
    		$system->db->extraiTodos("
				SELECT	TM.*
				FROM	TIPO_MARCA_VEICULO TM
				ORDER	BY TM.descricao
			")
   		);
    }
    
	/**
	 * Exclui um veículo
	 *
	 * @param integer $codVeículo
	 * @return array
	 */
	public static function exclui($codVeiculo) {
		global $system;
		
		/** Verifica se o veículo existe **/
		if (MCVeiculo::existe($codVeiculo) == false) return ('Erro: Veículo não existe');
		
		try {
			$system->db->con->beginTransaction ();
			
			/** Apaga o Veículo **/ 
			$system->db->Executa ("DELETE FROM VEICULOS WHERE codVeiculo = ?", array ($codVeiculo) );
			$system->db->con->commit ();
			return (null);
		} catch ( Exception $e ) {
			$system->db->con->rollback ();
			return ('Erro: ' . $e->getMessage ());
		}
	}
}