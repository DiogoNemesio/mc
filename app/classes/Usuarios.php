<?php

/**
 * Usuários
 * 
 * @package: Usuarios
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Usuarios {

	/**
     * Construtor
     *
	 * @return void
	 */
	private function __construct() {
		global $log;

		$log->debug(__CLASS__.": nova Instância");
	}
	
	/**
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
		
		/** Checar se bloco já existe **/
		if (\Usuarios::existe ($usuario) == false && $codUsuario == null) {
			/** Inserir **/
			$err = \Usuarios::inserir($usuario,$nome, $senha, $email, $codTipo, $codStatus);
			if (is_numeric($err)) {
				$cod		= $err;
				$codUsuario	= $cod;
				if ($codCondominio != null)	\Usuarios::associaCondominio($cod,$codCondominio);
				
			}else{
				return('Erro: '.$err);
			}
		}else{
			/** Atualizar **/
			return(\Usuarios::update($codUsuario,$usuario, $nome, $senha, $email, $codTipo,$codStatus));
		}
    }
	
	/**
     * Inserir o usuário no banco
     *
     * @param integer $usuario
     * @return array
     */
    public static function inserir ($usuario,$nome, $senha, $email, $codTipo, $codStatus) {
		global $log,$db;
		
		try {
			$db->con->beginTransaction();
			$db->Executa("INSERT INTO USUARIOS (codUsuario,usuario,nome,senha,email,codTipo) VALUES (null,?,?,?,?,?,?)",
				array($usuario,$nome,\Usuarios::crypt($usuario, $senha),$email,$codTipo,$codStatus)
			);

			$cod	= $db->con->lastInsertId();
			$db->con->commit();
			if (!$cod) {
				return('Erro:Não foi possível resgatar o código');
			}else{
				return($cod);
			}
		} catch (\Exception $e) {
			$db->con->rollback();
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
		global $db;
		
		if (!$codUsuario || !$codCondominio) DHCErro::halt(__CLASS__.': Falta de parâmetros');
		
		if (\Usuarios::temAcessoAoCondominio($codUsuario, $codCondominio) == true) {
			return ("Erro: Usuário já está associado a esse condomínio");
		}
		
		try {
			$db->con->beginTransaction();
			$db->Executa("INSERT INTO USUARIO_CONDOMINIO (codUsuario,codCondominio) VALUES (?,?)",
				array($codUsuario,$codCondominio)
			);
			
			$db->con->commit();
			
		} catch (\Exception $e) {
			$db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }
    
    /**
     * Desassociar o usuário do condomínio
     *
     * @param integer $usuario
     * @param integer $codCondominio
     * @return boolean
     */
    public static function desassociaCondominio ($codUsuario,$codCondominio) {
		global $db;
		
		if (!$codUsuario || !$codCondominio) DHCErro::halt(__CLASS__.': Falta de parâmetros');
		
		if (\Usuarios::temAcessoAoCondominio($codUsuario, $codCondominio) == false) {
			return ("Erro: Usuário não está associado a esse condomínio");
		}
		
		try {
			$db->con->beginTransaction();
			$db->Executa("DELETE FROM USUARIO_CONDOMINIO WHERE codUsuario = ? and codCondominio = ?",
				array($codUsuario,$codCondominio)
			);
			
			$db->con->commit();
			
		} catch (\Exception $e) {
			$db->con->rollback();
			return('Erro: '.$e->getMessage());
		}
    }
    
    /**
     * Atualizar o usuário no banco
     * @param integer $codUsuario
     * @param string $usuario
     * @param string $nome
     * @param string $senha
     * @param string $email
     * @param integer $codTipo
     * @param integer $codStatus
     * @return NULL|string
     */
    public static function update ($codUsuario,$usuario, $nome, $senha, $email, $codTipo, $codStatus) {
		global $db;
		
		if ($senha != null) {
				
			try {
				$db->con->beginTransaction();
				$db->Executa("
					UPDATE USUARIOS
					SET		usuario			= ?,
							nome			= ?,
							senha			= ?,
							email			= ?,
							codTipo			= ?,
							codStatus		= ?
					WHERE	codUsuario		= ?",
					array($usuario,$nome,\Usuarios::crypt($usuario, $senha),$email,$codTipo,$codStatus,$codUsuario)
				);
				$db->con->commit();
				return(null);
			} catch (\Exception $e) {
				$db->con->rollback();
				return('Erro: '.$e->getMessage());
			}
		}else{
			try {
				$db->con->beginTransaction();
				$db->Executa("
					UPDATE USUARIOS
					SET		usuario			= ?,
							nome			= ?,
							email			= ?,
							codTipo			= ?,
							codStatus		= ?
					WHERE	codUsuario		= ?",
					array($usuario,$nome,$email,$codTipo,$codStatus,$codUsuario)
				);
				$db->con->commit();
				return(null);
			} catch (\Exception $e) {
				$db->con->rollback();
				return('Erro: '.$e->getMessage());
			}
		}
    }

    /**
     * Lista usuários de um condomínio
     *
     * @return \Zend\Db\ResultSet
     */
    public static function lista () {
		global $db;
		
    	return (
    		$db->extraiTodos("
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
     * @param unknown $codCondominio
     * @param unknown $codTipo
     * @return \Zend\Db\ResultSet
     */
    public static function listaPorTipo ($codCondominio,$codTipo) {
		global $db;
		
    	return (
    		$db->extraiTodos("
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
     * @return \Zend\Db\ResultSet
     */
    public static function listaSindicos () {
		global $db;
						
    	return (
    		$db->extraiTodos("
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
     * @return \Zend\Db\ResultSet
     */
    public static function listaAdmin () {
		global $db;
						
    	return (
    		$db->extraiTodos("
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
     * @return \Zend\Db\ResultSet
     */
    public static function listaSubSindicos () {
		global $db;
						
    	return (
    		$db->extraiTodos("
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
     * @param integer $codTipo
     * @return \Zend\Db\ResultSet
     */
    public static function listaTipoUsuario ($codTipo = null) {
		global $db;
		
		if ($codTipo != null) {
			$where	= "WHERE TP.codTipo		= '".$codTipo."'";
		}else{
			$where	= "";
		}
						
    	return (
    		$db->extraiTodos("
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
     * @return \Zend\Db\ResultSet
     */
    public static function listaTipoStatus() {
		global $db;
		
    	return (
    		$db->extraiTodos("
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
     * @return boolean
     */
    public static function existe ($usuario) {
		global $db;
		
    	$info = $db->extraiPrimeiro("
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
     * @return boolean
     */
    public static function existeCodigo ($codUsuario) {
		global $db;
		
    	$info = $db->extraiPrimeiro("
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
     * @return \Zend\Db\ResultSet
     */
    public static function getInfo ($codUsuario) {
		global $db;
			
    	return (
    		$db->extraiPrimeiro("
				SELECT	U.*,TU.descricao
				FROM	USUARIOS U, TIPO_USUARIO TU
				WHERE   U.codTipo	 = TU.codTipo
				AND 	U.codUsuario = '".$codUsuario."'

			")
   		);	
    }

	
    /**
     * Verifica se o usuário tem acesso a um determinado condomínio
     * @param number $codUsuario
     * @param number $codCondominio
     * @return boolean
     */
	public static function temAcessoAoCondominio($codUsuario,$codCondominio) {
		global $db;
			
    	$return =  $db->extraiPrimeiro("
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
     * Resgatar o condomínio do síndico
     * @param string $codUsuario
     * @return integer
     */
    public static function getCondominio($codUsuario) {
		global $db;
			
    	$return =  $db->extraiPrimeiro("
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
	 * Retorna os condomínios que o usuário não tem acesso
	 * @param integer $codUsuario
	 * @param string $nome
     * @return \Zend\Db\ResultSet
	 */
    public static function getCondominiosSemAcesso($codUsuario,$nome = null) {
		global $db;
		
		if ($nome != null) {
			$and	= "AND		(C.nomeCondominio LIKE '%".$nome."%' OR C.condominio LIKE '%".$nome."%') "; 
		}else{
			$and	= "";
		}
			
    	return($db->extraiTodos("
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
	 * Retorna os condomínios que o usuário tem acesso
	 * @param string $codUsuario
     * @return \Zend\Db\ResultSet
	 */
    public static function getCondominiosComAcesso($codUsuario) {
		global $db;
		
    	return($db->extraiTodos("
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
	 * @return null||string
	 */
	public static function exclui($codUsuario) {
		global $db;
		
		/** Verifica se o Usuário existe **/
		if (\Usuarios::existeCodigo($codUsuario) == false) return ('Erro: Usuário não existe');
		
		
		try {
			$db->con->beginTransaction ();
			
			/** Desassocia o usuário dos condomínios **/ 
			$db->Executa ("DELETE FROM USUARIO_CONDOMINIO WHERE codUsuario = ?", array ($codUsuario) );
			
			/** Apaga o Usuário **/ 
			$db->Executa ("DELETE FROM USUARIOS WHERE codUsuario = ?", array ($codUsuario) );
			$db->con->commit ();
			return (null);
		} catch ( \Exception $e ) {
			$db->con->rollback ();
			return ('Erro: ' . $e->getMessage ());
		}
	}
}