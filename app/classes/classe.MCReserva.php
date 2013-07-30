<?php

/**
 * MCReserva
 * 
 * @package: MCReserva
 * @created: 23/04/2012
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class MCReserva {

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
	 * Salvar Uma Reserva
	 * 
	 * @param int $codReserva
	 * @param unknown_type $codUnidade
	 * @param unknown_type $codEspaco
	 * @param unknown_type $dataInicial
	 * @param unknown_type $dataFinal
	 * @param unknown_type $codUsuario
	 * @param unknown_type $indConfirmado
	 */
    public static function salva ($codReserva, $codUnidade, $codEspaco, $dataInicial, $dataFinal, $codUsuario, $indConfirmado) {
		global $system;
		
		/** Checar se Reserva já existe **/
		if (MCReserva::existe($codReserva) == false || $codReserva == null) {

			/** Inserir **/
			$err = MCReserva::inserir($codUnidade, $codEspaco, $dataInicial, $dataFinal, $codUsuario, $indConfirmado);
			if (is_numeric($err)) {
				$codReserva	= $err;
			}else{
				return('Erro: '.$err);
			}
		}else{
			/** Atualizar **/
			return(MCReserva::update($codReserva, $codUnidade, $codEspaco, $dataInicial, $dataFinal, $codUsuario, $indConfirmado));
		}
    }
	
    /**
     * 
     * Inserir a Reserva no banco
     * @param unknown_type $codUnidade
     * @param unknown_type $codEspaco
     * @param unknown_type $dataInicial
     * @param unknown_type $dataFinal
     * @param unknown_type $codUsuario
     * @param unknown_type $indConfirmado
     */
    public static function inserir ($codUnidade, $codEspaco, $dataInicial, $dataFinal, $codUsuario, $indConfirmado) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO RESERVAS (codReserva, codUnidade, codEspaco, dataInicial, dataFinal, codUsuario, indConfirmado) VALUES (null,?,?,?,?,?,?)",
				array($codReserva, $codUnidade, $codEspaco, $dataInicial, $dataFinal, $codUsuario, $indConfirmado)
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
     * Atualizar os dados da reserva
     * @param unknown_type $codReserva
     * @param unknown_type $codUnidade
     * @param unknown_type $codEspaco
     * @param unknown_type $dataInicial
     * @param unknown_type $dataFinal
     * @param unknown_type $codUsuario
     * @param unknown_type $indConfirmado
     */
    public static function update ($codReserva, $codUnidade, $codEspaco, $dataInicial, $dataFinal, $codUsuario, $indConfirmado) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("
				UPDATE	RESERVAS
				SET		codUnidade		= ?,
						codEspaco		= ?,
						dataInicial		= ?,
						dataFinal		= ?,
						codUsuario		= ?,
						indConfirmado	= ?
				WHERE	codReserva		= ?",
				array($codUnidade, $codEspaco, $dataInicial, $dataFinal, $codUsuario, $indConfirmado, $codReserva)
			);
			$system->db->con->commit();
			return(null);
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }

    /**
     * Lista as reservas do condominio
     *
     * @param integer $codCondominio
     * @return array
     */
    public static function lista ($codCondominio) {
		global $system;
						
    	return (
    		$system->db->extraiTodos("
				SELECT	R.*,UN.nome NOME_UNIDADE,U.nome NOME_USUARIO,E.nome NOME_ESPACO
				FROM	RESERVAS	R,
						ESPACOS		E,
						UNIDADES	UN,
						BLOCOS		B,
						USUARIOS	U
				WHERE	R.codUnidade		= UN.codUnidade
				AND		R.codEspaco			= E.codEspaco
				AND		UN.codBloco			= B.codBloco
				AND		R.codUsuario		= U.codUsuario
				AND		B.codCondominio 	= '".$codCondominio."'
				ORDER	BY R.dataInicial desc
			")
   		);
    }
    
    /**
     * Verifica se a reserva existe
     *
     * @param integer $codReserva
     * @return array
     */
    public static function existe ($codReserva) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT COUNT(*) NUM
				FROM 	RESERVAS R
				WHERE 	R.codReserva		= '".$codReserva."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    	
    }
    
    /**
	 * Lista as reservas por Unidade
	 *
	 * @param integer $codUnidade
	 * @return array
	 */
	public static function listaPorUnidade($codUnidade) {
		global $system;
		
		return ($system->db->extraiTodos ( "
				SELECT	R.*,UN.nome NOME_UNIDADE,U.nome NOME_USUARIO,E.nome NOME_ESPACO
				FROM	RESERVAS	R,
						ESPACOS		E,
						UNIDADES	UN,
						USUARIOS	U
				WHERE	R.codUnidade		= UN.codUnidade
				AND		R.codEspaco			= E.codEspaco
				AND		R.codUsuario		= U.codUsuario
				AND		R.codUnidade	 	= '".$codUnidade."'
				ORDER	BY R.dataInicial
		" ));
	}
	
	/**
	 * 
	 * Lista as reservas por intervalo de data
	 * @param integer $codUnidade
	 * @param date $dataInicial
	 * @param date $dataFinal
	 *
	 * @return array
	 */
	public static function listaPorData($codUnidade,$dataInicial, $dataFinal) {
		global $system;
		
		return ($system->db->extraiTodos ( "
				SELECT	R.*,UN.nome NOME_UNIDADE,U.nome NOME_USUARIO,E.nome NOME_ESPACO
				FROM	RESERVAS	R,
						ESPACOS		E,
						UNIDADES	UN,
						BLOCOS		B,
						USUARIOS	U
				WHERE	R.codUnidade		= UN.codUnidade
				AND		R.codEspaco			= E.codEspaco
				AND		UN.codBloco			= B.codBloco
				AND		R.codUsuario		= U.codUsuario
				AND		B.codCondominio 	= '".$codCondominio."'
				AND		R.dataInicial		>= '".$dataInicial."'
				AND		R.dataInicial		<= '".$dataFinal."'
				ORDER	BY R.dataInicial
				" ));
	}
	
	
	/**
     * Resgata as informações de uma Reserva
     *
     * @param integer $codReserva
     * @return array
     */
    public static function getInfo ($codReserva) {
		global $system;
			
    	return (
    		$system->db->extraiPrimeiro("
				SELECT	R.*,UN.nome NOME_UNIDADE,U.nome NOME_USUARIO,E.nome NOME_ESPACO
				FROM	RESERVAS	R,
						ESPACOS		E,
						UNIDADES	UN,
						USUARIOS	U
				WHERE	R.codUnidade		= UN.codUnidade
				AND		R.codUsuario		= U.codUsuario
				AND		R.codEspaco			= E.codEspaco
				AND		R.codReserva	 	= '".$codReserva."'
				ORDER	BY R.dataInicial
			")
   		);	
    }

	/**
	 * Exclui a reserva do banco
	 *
	 * @param integer $codReserva
	 * @return array
	 */
	public static function exclui($codReserva) {
		global $system;
		
		/** Verifica se a Reserva existe **/
		if (MCReserva::existe($codReserva) == false) return ('Erro: reserva não existe');
		
		try {
			$system->db->con->beginTransaction ();
			
			/** Apaga a reserva **/ 
			$system->db->Executa ("DELETE FROM RESERVAS WHERE codReserva = ?", array ($codReserva) );
			$system->db->con->commit ();
			return (null);
		} catch ( Exception $e ) {
			$system->db->con->rollback ();
			return ('Erro: ' . $e->getMessage ());
		}
	}
    
}