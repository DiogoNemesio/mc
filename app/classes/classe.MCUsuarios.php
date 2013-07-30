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

class MCUsuarios {

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
	 * Criptografar a senha
	 * @param string $usuario
	 * @param string $senha
	 */
	public static function crypt($usuario,$senha) {
		return md5('MC'.$usuario.'|'.$senha);
	}
	
	/**
     * Salvar usuário
     *
     * @param integer $usuario
     * @return array
     */
     public static function salva (&$codUsuario, $usuario,$nome, $senha, $email, $codTipo, $codStatus, $codCondominio ) {
		global $system;
		
		/** Checar se bloco já existe **/
		if (MCUsuarios::existe ($usuario) == false && $codUsuario == null) {
			/** Inserir **/
			$err = MCUsuarios::inserir($usuario,$nome, $senha, $email, $codTipo, $codStatus);
			if (is_numeric($err)) {
				$cod		= $err;
				$codUsuario	= $cod;
				if ($codCondominio != null)	MCUsuarios::associaCondominio($cod,$codCondominio);
				
			}else{
				return('Erro: '.$err);
			}
		}else{
			/** Atualizar **/
			return(MCUsuarios::update($codUsuario,$usuario, $nome, $senha, $email, $codTipo,$codStatus));
		}
    }
	
	/**
     * Inserir o condomínio no banco
     *
     * @param integer $usuario
     * @return array
     */
    public static function inserir ($usuario,$nome, $senha, $email, $codTipo, $codStatus) {
		global $system;
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO USUARIOS (codUsuario,usuario,nome,senha,email,codTipo) VALUES (null,?,?,?,?,?,?)",
				array($usuario,$nome,MCUsuarios::crypt($usuario, $senha),$email,$codTipo,$codStatus)
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
     * Associar o usuário ao condomínio
     *
     * @param integer $usuario
     * @return array
     */
    public static function associaCondominio ($codUsuario,$codCondominio) {
		global $system;
		
		if (!$codUsuario || !$codCondominio) DHCErro::halt(__CLASS__.': Falta de parâmetros');
		
		if (MCUsuarios::temAcessoAoCondominio($codUsuario, $codCondominio) == true) {
			return ("Erro: Usuário já está associado a esse condomínio");
		}
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO USUARIO_CONDOMINIO (codUsuario,codCondominio) VALUES (?,?)",
				array($codUsuario,$codCondominio)
			);
			
			$system->db->con->commit();
			
		} catch (Exception $e) {
			$system->db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }
    
    /**
     * Desassociar o usuário ao condomínio
     *
     * @param integer $usuario
     * @return array
     */
    public static function desassociaCondominio ($codUsuario,$codCondominio) {
		global $system;
		
		if (!$codUsuario || !$codCondominio) DHCErro::halt(__CLASS__.': Falta de parâmetros');
		
		if (MCUsuarios::temAcessoAoCondominio($codUsuario, $codCondominio) == false) {
			return ("Erro: Usuário não está associado a esse condomínio");
		}
		
		try {
			$system->db->con->beginTransaction();
			$system->db->Executa("DELETE FROM USUARIO_CONDOMINIO WHERE codUsuario = ? and codCondominio = ?",
				array($codUsuario,$codCondominio)
			);
			
			$system->db->con->commit();
			
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
    public static function update ($codUsuario,$usuario, $nome, $senha, $email, $codTipo, $codStatus) {
		global $system;
		
		if ($senha !=null) {
				
			try {
				$system->db->con->beginTransaction();
				$system->db->Executa("
					UPDATE USUARIOS
					SET		usuario			= ?,
							nome			= ?,
							senha			= ?,
							email			= ?,
							codTipo			= ?,
							codStatus		= ?
					WHERE	codUsuario		= ?",
					array($usuario,$nome,MCUsuarios::crypt($usuario, $senha),$email,$codTipo,$codStatus,$codUsuario)
				);
				$system->db->con->commit();
				return(null);
			} catch (Exception $e) {
				$system->db->con->rollback();
				return('Erro: '.$e->getMessage());
			}
		}else{
			try {
				$system->db->con->beginTransaction();
				$system->db->Executa("
					UPDATE USUARIOS
					SET		usuario			= ?,
							nome			= ?,
							email			= ?,
							codTipo			= ?,
							codStatus		= ?
					WHERE	codUsuario		= ?",
					array($usuario,$nome,$email,$codTipo,$codStatus,$codUsuario)
				);
				$system->db->con->commit();
				return(null);
			} catch (Exception $e) {
				$system->db->con->rollback();
				return('Erro: '.$e->getMessage());
			}
		}
    }

    /**
     * Lista usuários de um condomínio
     *
     * @return array
     */
    public static function lista () {
		global $system;
		
    	return (
    		$system->db->extraiTodos("
				SELECT	U.*, TU.descricao,TSU.descricao status
				FROM	TIPO_USUARIO TU,
						USUARIOS U,
						TIPO_STATUS_USUARIO TSU 
				WHERE	TU.codTipo		= U.codTipo
				AND		TSU.codTipo		= U.codStatus 
				ORDER	BY U.nome
			")
   		);
    }

    
    /**
     * Lista usuários de um condomínio e de um determinado tipo
     *
     * @return array
     */
    public static function listaPorTipo ($codCondominio,$codTipo) {
		global $system;
		
    	return (
    		$system->db->extraiTodos("
				SELECT	U.*, TU.descricao,TSU.descricao status
				FROM	TIPO_USUARIO TU,
						USUARIOS U,
						USUARIO_CONDOMINIO UC,
						TIPO_STATUS_USUARIO TSU
				WHERE	TU.codTipo			= U.codTipo
				AND		UC.codUsuario		= U.codUsuario
				AND		TSU.codTipo			= U.codStatus
				AND		UC.codCondominio 	= '".$codCondominio."'
				AND		U.codTipo			= '".$codTipo."'
				ORDER	BY U.nome
			")
   		);
    }
    
    /**
     * Lista todos os sindicos
     *
     * @return array
     */
    public static function listaSindicos () {
		global $system;
						
    	return (
    		$system->db->extraiTodos("
				SELECT	U.*
				FROM	USUARIOS U
				WHERE 	U.codTipo = 'S'
				ORDER	BY nome
			")
   		);
    }
    
     /**
     * Lista administradores 
     *
     * @return array
     */
    public static function listaAdmin () {
		global $system;
						
    	return (
    		$system->db->extraiTodos("
				SELECT	U.*
				FROM	USUARIOS U
				WHERE 	U.codTipo = 'A'
				ORDER	BY nome
			")
   		);
    }
    
      /**
     * Lista os sub-sindicos 
     *
     * @return array
     */
    public static function listaSubSindicos () {
		global $system;
						
    	return (
    		$system->db->extraiTodos("
				SELECT	U.*
				FROM	USUARIOS U
				WHERE 	U.codTipo = 'SS'
				ORDER	BY nome
			")
   		);
    }
    
    /**
     * Lista os tipos de usuários
     *
     * @return array
     */
    public static function listaTipoUsuario ($codTipo = null) {
		global $system;
		
		if ($codTipo != null) {
			$where	= "WHERE TP.codTipo		= '".$codTipo."'";
		}else{
			$where	= "";
		}
						
    	return (
    		$system->db->extraiTodos("
				SELECT	TP.*
				FROM	TIPO_USUARIO TP
				$where
				ORDER	BY descricao
			")
   		);
    }
    
    /**
     * Lista os tipos de status
     *
     * @return array
     */
    public static function listaTipoStatus() {
		global $system;
		
    	return (
    		$system->db->extraiTodos("
				SELECT	TS.*
				FROM	TIPO_STATUS_USUARIO TS
				ORDER	BY descricao
			")
   		);
    }
    
    /**
     * Verifica se o Usuário existe
     *
     * @param string $usuario
     * @return array
     */
    public static function existe ($usuario) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT 	COUNT(*) NUM
				FROM 	USUARIOS U
				WHERE 	U.usuario = '".$usuario."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }

    /**
     * Verifica se o Código do Usuário existe
     *
     * @param integer $usuario
     * @return array
     */
    public static function existeCodigo ($codUsuario) {
		global $system;
		
    	$info = $system->db->extraiPrimeiro("
				SELECT 	COUNT(*) NUM
				FROM 	USUARIOS U
				WHERE 	U.codUsuario = '".$codUsuario."'
		");
    	
    	if ($info->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}
    }
    
    /**
     * Resgata as informações do condomínio
     *
     * @param integer $usuario
     * @return array
     */
    public static function getInfo ($codUsuario) {
		global $system;
			
    	return (
    		$system->db->extraiPrimeiro("
				SELECT	U.*,TU.descricao
				FROM	USUARIOS U, TIPO_USUARIO TU
				WHERE   U.codTipo	 = TU.codTipo
				AND 	U.codUsuario = '".$codUsuario."'

			")
   		);	
    }

	
    /**
     * 
     * Verifica se o usuário tem acesso a um determinado condomínio
     * @param number $codUsuario
     * @param number $codCondominio
     */
	public static function temAcessoAoCondominio($codUsuario,$codCondominio) {
		global $system;
			
    	$return =  $system->db->extraiPrimeiro("
				SELECT	COUNT(*) NUM 
				FROM	USUARIO_CONDOMINIO UC
				WHERE   UC.codUsuario 		= '".$codUsuario."'
				AND		UC.codCondominio	= '".$codCondominio."'
		");
    	
    	if ($return->NUM > 0) {
    		return true;
    	}else{
    		return false;
    	}

    }
	
    /**
     * 
     * Resgatar o condomínio do síndico
     * @param string $codUsuario
     */
    public static function getCondominio($codUsuario) {
		global $system;
			
    	$return =  $system->db->extraiPrimeiro("
				SELECT	UC.codCondominio
				FROM	USUARIO_CONDOMINIO UC
				WHERE   UC.codUsuario 		= '".$codUsuario."'
		");
    	
    	if (isset ($return->codCondominio)) {
    		return ($return->codCondominio);
    	}else{
    		return null;
    	}
    }


	/**
	 * 
	 * Retorna os condomínios que o usuário não tem acesso
	 * @param string $codUsuario
	 */
    public static function getCondominiosSemAcesso($codUsuario,$nome = null) {
		global $system;
		
		if ($nome != null) {
			$and	= "AND		(C.nomeCondominio LIKE '%".$nome."%' OR C.condominio LIKE '%".$nome."%') "; 
		}else{
			$and	= "";
		}
			
    	return($system->db->extraiTodos("
				SELECT	C.*
				FROM	CONDOMINIOS C
				WHERE   C.codCondominio NOT IN (
					SELECT	codCondominio
					FROM	USUARIO_CONDOMINIO UC
					WHERE	UC.codUsuario 		= '".$codUsuario."'
				)
				$and
		"));
    }
    
	/**
	 * 
	 * Retorna os condomínios que o usuário tem acesso
	 * @param string $codUsuario
	 */
    public static function getCondominiosComAcesso($codUsuario) {
		global $system;
		
    	return($system->db->extraiTodos("
				SELECT	C.*
				FROM	CONDOMINIOS C,
						USUARIO_CONDOMINIO UC
				WHERE   C.codCondominio 	= UC.codCondominio
				AND		UC.codUsuario 		= '".$codUsuario."'
		"));
    }
    
	/**
	 * Exclui um Usuário
	 *
	 * @param integer $codUsuario
	 * @return array
	 */
	public static function exclui($codUsuario) {
		global $system;
		
		/** Verifica se o Usuário existe **/
		if (MCUsuarios::existeCodigo($codUsuario) == false) return ('Erro: Usuário não existe');
		
		
		try {
			$system->db->con->beginTransaction ();
			
			/** Desassocia o usuário dos condomínios **/ 
			$system->db->Executa ("DELETE FROM USUARIO_CONDOMINIO WHERE codUsuario = ?", array ($codUsuario) );
			
			/** Apaga o Usuário **/ 
			$system->db->Executa ("DELETE FROM USUARIOS WHERE codUsuario = ?", array ($codUsuario) );
			$system->db->con->commit ();
			return (null);
		} catch ( Exception $e ) {
			$system->db->con->rollback ();
			return ('Erro: ' . $e->getMessage ());
		}
	}
    
    
    
}