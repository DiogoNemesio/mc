<?php

/**
 * Condomínio
 * 
 * @package: MCCondominio
 * @created: 15/10/2010
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class MCCondominio {
	
	/**
	 * Construtor
	 *
	 * @return void
	 */
	private function __construct() {
		global $system;
		
		$system->log->debug->debug ( __CLASS__ . ": nova Instância" );
	}
	
	/**
	 * Salvar condominío
	 *
	 * @param integer $usuario
	 * @return array
	 */
	public static function salva(&$codCondominio, $nomeCondominio, $idCondominio, $endereco, $bairro, $numero, $codCidade, $cep, $numUnidades) {
		global $system;
		
		/** Checar se condomínio já existe **/
		if (($codCondominio == null) && (MCCondominio::existe ( $codCondominio, $idCondominio ) == false)) {
			
			/** Inserir **/
			$err = MCCondominio::inserir ( $nomeCondominio, $idCondominio, $endereco, $bairro, $numero, $codCidade, $cep, $numUnidades );
			
			if (is_numeric ( $err )) {
				$codCondominio = $err;
				
				/** Cria a estrutura **/
				$err = MCCondominio::criaEstrutura ( $codCondominio, $idCondominio );
				
				if ($err) return ($err);
				
				/** Insere o parâmetro **/
				$err = MCCondParametro::salva ( $codCondominio );
				if ($err) return ($err);
				
				/** Cadastro o bloco Único **/
				$err = MCBloco::inserir($codCondominio, 'Bloco Único', 'Bloco Único');
				if ($err) return ($err);
			
			} else {
				return ('Erro: ' . $err);
			}
		} else {
			
			/** Resgata as informações antigas do condomínio **/
			$info	= MCCondominio::getInfo($codCondominio);

			/** Verifica se a identificação mudou **/
			if ($idCondominio != $info->condominio) {
				if (file_exists ( DOC_ROOT . $info->condominio )) {
					/** Exclui a estrutura antiga **/
					$err = MCCondominio::excluiEstrutura($codCondominio);
				}
				
				/** Cria a estrutura nova **/
				$err = MCCondominio::criaEstrutura ( $codCondominio, $idCondominio );
								
			}
			
			/** Atualizar **/
			return (MCCondominio::update ( $codCondominio, $nomeCondominio, $idCondominio, $endereco, $bairro, $numero, $codCidade, $cep, $numUnidades ));
		}
	}
	
	/**
	 * Inserir o condomínio no banco
	 *
	 * @param integer $usuario
	 * @return array
	 */
	public static function inserir($nomeCondominio, $idCondominio, $endereco, $bairro, $numero, $codCidade, $cep, $numUnidades) {
		global $system;
		
		try {
			$system->db->con->beginTransaction ();
			$system->db->Executa ( "INSERT INTO CONDOMINIOS (codCondominio,nomeCondominio,condominio,endereco,bairro,numero,cep,codCidade,qtdeUnidades) VALUES (null,?,?,?,?,?,?,?,?)", array ($nomeCondominio, $idCondominio, $endereco, $bairro, $numero, $cep, $codCidade, $numUnidades ) );
			$cod = $system->db->con->lastInsertId ();
			$system->db->con->commit ();
			
			if (! $cod) {
				return ('Erro:Não foi possível resgatar o código');
			} else {
				return ($cod);
			}
		} catch ( Exception $e ) {
			$system->db->con->rollback ();
			return ('Erro: ' . $e->getMessage ());
		}
	}
	
	/**
	 * Atualizar o condomínio no banco
	 *
	 * @param integer $usuario
	 * @return array
	 */
	public static function update($codCondominio, $nomeCondominio, $idCondominio, $endereco, $bairro, $numero, $codCidade, $cep, $numUnidades) {
		global $system;
		
		try {
			$system->db->con->beginTransaction ();
			$system->db->Executa ( "
				UPDATE CONDOMINIOS 
				SET		nomeCondominio	= ?,
						condominio		= ?,
						endereco		= ?,
						bairro			= ?,
						numero			= ?,
						cep				= ?,
						codCidade		= ?,
						qtdeUnidades	= ?
				WHERE	codCondominio	= ?", array ($nomeCondominio, $idCondominio, $endereco, $bairro, $numero, $cep, $codCidade, $numUnidades, $codCondominio ) );
			$system->db->con->commit ();
			return (null);
		} catch ( Exception $e ) {
			$system->db->con->rollback ();
			return ('Erro: ' . $e->getMessage ());
		}
	}
	
	/**
	 * Resgata os menus por tipo de usuário
	 *
	 * @param integer $usuario
	 * @return array
	 */
	public static function lista($codCondominio = null, $nome = null) {
		global $system;
		$where = null;
		
		if ($codCondominio != null)
			$where .= "AND	C.codCondominio 	= '" . $codCondominio . "'";
		if ($nome != null)
			$where .= "AND	C.nomeCondominio 	LIKE '%" . $nome . "%'";
		
		return ($system->db->extraiTodos ( "
				SELECT	C.*,E.*
				FROM	CONDOMINIOS C,
						CIDADES		CI,
						ESTADOS		E
				WHERE	C.codCidade			= CI.codCidade
				AND		CI.codEstado		= E.codEstado
				$where
				ORDER	BY nomeCondominio
			" ));
	}
	
	/**
	 * Verifica se o condomínio existe
	 *
	 * @param integer $usuario
	 * @return array
	 */
	public static function existe($codCondominio = null, $idCondominio = null) {
		global $system;
		$where = null;
		
		if ($codCondominio != null)
			$where .= "AND	C.codCondominio 	= '" . $codCondominio . "'";
		if ($idCondominio != null)
			$where .= "AND	C.condominio		= '" . $idCondominio . "'";
		
		$info = $system->db->extraiPrimeiro ( "
				SELECT	COUNT(*) NUM
				FROM	CONDOMINIOS C
				WHERE	1=1
				$where
		" );
		
		if ($info->NUM > 0) {
			return true;
		} else {
			return false;
		}
	
	}
	
	/**
	 * Cria a estrutura de diretórios
	 *
	 * @param integer $codCondominio
	 * @param varchar $idCondominio
	 * @return boolean
	 */
	public static function criaEstrutura($codCondominio, $idCondominio) {
		global $system;
		
		$dir = DOC_ROOT . $idCondominio;
		
		return null;
		
		/** Checar se existe a pasta **/
		if (file_exists ( $dir )) {
			return ('Erro:Identificação já existe !!!');
		} else {
			
			try {
				/** Cria o diretório **/
				if (! mkdir ( $dir ))
					return ('Erro:Erro ao criar estrutura de identificação (1) !!!');
				
				/** Altera as permissões do diretório **/
				chmod ( $dir, 0775 );
				
				/** Cria o Link **/
				//$system->log->debug->debug('Default index:' .DEF_INDEX_PATH.' Dir: '.$dir);
				if (! symlink ( DEF_INDEX_PATH, $dir . '/index.php' ))
					return ('Erro:Erro ao criar estrutura de identificação (2) !!!');
			
		//if (!copy(DEF_INDEX_PATH,$dir.'/index.php')) return('Erro:Erro ao criar estrutura de identificação (2) !!!');
			

			} catch ( Exception $e ) {
				return ('Erro: ' . $e->getMessage ());
			}
		
		}
		
		return null;
	
	}
	
	/**
	 * Apaga a estrutura de diretórios
	 *
	 * @param integer $codCondominio
	 * @return boolean
	 */
	public static function excluiEstrutura($codCondominio) {
		global $system;
		
		return null;
		
		/** Resgata as informações do condomínio **/
		$info = MCCondominio::getInfo ( $codCondominio );
		
		if (! isset ( $info->condominio )) {
			return ('Erro: condomínio não encontrado !');
		}
		
		$dir = DOC_ROOT . $info->condominio;
		
		/** Checar se existe a pasta **/
		if (file_exists ( $dir )) {
			try {
				/** Exclui o link simbólico **/
				if (! unlink ( $dir . '/index.php' ))
					return ('Erro:Erro ao apagar estrutura de identificação (1) !!!');
				
				/** Exclui o diretório **/
				if (! rmdir ( $dir ))
					return ('Erro:Erro ao apagar estrutura de identificação (2) !!!');
			
			} catch ( Exception $e ) {
				return ('Erro: ' . $e->getMessage ());
			}
		}
		return null;
	}
	
	/**
	 * Resgata as informações do condomínio
	 *
	 * @param integer $usuario
	 * @return array
	 */
	public static function getInfo($codCondominio = null, $idCondominio = null) {
		global $system;
		$where = null;
		
		if ($codCondominio != null)
			$where .= "AND	C.codCondominio = '" . $codCondominio . "'";
		if ($idCondominio != null)
			$where .= "AND	C.condominio	= '" . $idCondominio . "'";
		
		if ((!$codCondominio) && (!$idCondominio)) return false;
		return ($system->db->extraiPrimeiro ( "
				SELECT	C.*,E.*
				FROM	CONDOMINIOS C,
						CIDADES		CI,
						ESTADOS		E
				WHERE	C.codCidade			= CI.codCidade
				AND		CI.codEstado		= E.codEstado
				$where
			" ));
	}
	
	/**
	 * Lista os usuários do Condomínio
	 *
	 * @param integer $codCondominio
	 * @return array
	 */
	public static function listaUsuarios($codCondominio) {
		global $system;
		
		return ($system->db->extraiTodos ( "
				SELECT	U.*
				FROM	USUARIOS			U,
						USUARIO_CONDOMINIO	UC
				WHERE	U.codUsuario		= UC.codUsuario
				and		UC.codCondominio	= '" . $codCondominio . "'
				ORDER	BY U.codUsuario
			" ));
	}
	
	/**
	 * Lista os blocos do Condomínio
	 *
	 * @param integer $codCondominio
	 * @return array
	 */
	public static function listaBlocos($codCondominio) {
		global $system;
		
		return ($system->db->extraiTodos ( "
				SELECT	B.*
				FROM	BLOCOS		B
				WHERE	B.codCondominio	= '" . $codCondominio . "'
				ORDER	BY B.codBloco
			" ));
	}
	
	/**
	 * Exclui o condomínio do banco
	 *
	 * @param integer $codCondominio
	 * @return array
	 */
	public static function exclui($codCondominio) {
		global $system;
		
		/** Verifica se o condomínio existe **/
		if (MCCondominio::existe($codCondominio) == false) return ('Erro: condomínio não existe');
		
		/** Verifica se existem blocos cadastrado **/
		$blocos	= MCCondominio::listaBlocos($codCondominio);
		if (sizeof($blocos) > 0) return ('Erro: Existem blocos nesse condomínio');
		
		try {
			$system->db->con->beginTransaction ();
			
			/** Apaga os parâmetros **/
			MCCondParametro::exclui ($codCondominio);
			
			/** Apaga o condomínio **/ 
			$system->db->Executa ("DELETE FROM CONDOMINIOS WHERE codCondominio = ?", array ($codCondominio) );
			$system->db->con->commit ();
			return (null);
		} catch ( Exception $e ) {
			$system->db->con->rollback ();
			return ('Erro: ' . $e->getMessage ());
		}
	}
	
}