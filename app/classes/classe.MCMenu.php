<?php

/**
 * Menu
 * 
 * @package: MCMenu
 * @created: 07/10/2010
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class MCMenu {

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
     * Resgata os menus por tipo de usuário
     *
     * @param integer $usuario
     * @return array
     */
    public static function DBGetMenuItens($usuario) {
		global $system;
    	return (
    		$system->db->extraiTodos("
				SELECT	M.*
				FROM	MENU M,
						MENU_TIPO_USUARIO MTU,
						USUARIOS U
				WHERE	M.codMenu 			= MTU.codMenu
				AND		MTU.codTipoUsuario 	= U.codTipo
				AND		U.usuario			= '".$usuario."'
				ORDER	BY nivelArvore,Ordem
			")
   		);
    }
    
    /**
     * Resgata os menus por tipo de usuário
     *
     * @param integer $codTipoUsuario
     * @param integer $menuPai
     * @return array
     */
		public static function DBGetMenuItensTipoUsuario($codTipoUsuario,$menuPai = null) {
		global $system;
    	if ($menuPai != null) {
    		$where	= " AND	M.codMenuPai	= '".$menuPai."'";
    	}else{
    		$where	= " AND	M.nivelArvore	= '0'";
    	}
    	
    	return (
    		$system->db->extraiTodos("
				SELECT	M.*
				FROM	MENU M,
						MENU_TIPO_USUARIO MTU
				WHERE	M.codMenu 			= MTU.codMenu
				AND		MTU.codTipoUsuario 	= '".$codTipoUsuario."'
				$where
				ORDER	BY nivelArvore,Ordem
			")
   		);
    }

    /**
     * Resgata os menus que o tipo de usuário não possue
     *
     * @param integer $codTipoUsuario
     * @param integer $menuPai
     * @return array
     */
	public static function DBGetMenuIndispTipoUsuario($codTipoUsuario,$menuPai = null) {
		global $system;
    	if ($menuPai != null) {
    		$where	= "
    			AND		(M.codMenu			= '".$menuPai."'
						OR
						 M.codMenuPai		= '".$menuPai."'
						) ";
    	}else{
    		$where	= "AND	M.nivelArvore	= '0'";
    	}
    	
    	return (
    		$system->db->extraiTodos("
				SELECT	M.*
				FROM	MENU M
				WHERE	M.codMenu NOT IN (
					SELECT	codMenu
					FROM	MENU_TIPO_USUARIO MTU
					WHERE	MTU.codTipoUsuario 	= '".$codTipoUsuario."'
				)
				$where
				ORDER	BY nivelArvore
			")
   		);
    }

    /**
     * Resgata a lista de menus
     *
     * @param integer $codTipoUsuario
     * @param integer $menuPai
     * @return array
     */
    public static function DBGetListMenus($nivel = null) {
		global $system;
    	if ($nivel !== null) {
    		$where	= "	WHERE		M.nivelArvore = '".$nivel."'";
    	}else{
    		$where	= " ";
    	}
    	
    	return (
    		$system->db->extraiTodos("
				SELECT	M.*
				FROM	MENU M
				$where
				ORDER	BY menu
			")
   		);
    }

    
    /**
	 * Resgatar a lista de Tipos de Usuários
     */
    public static function DBGetListTipoMenu() {
		global $system;
    	return (
    		$system->db->extraiTodos("
	    		SELECT	*
	    		FROM	TIPO_MENU
	    		ORDER BY descricao
    		")
    	);
    }

    /**
     * Resgata os dados de um Menu
     *
     * @param integer $codMenu
     * @return array
     */
    public static function DBGetInfoMenu($codMenu) {
		global $system;
    	$return	= $system->db->extraiPrimeiro("
				SELECT	M.*
				FROM	MENU M
				WHERE	M.codMenu 			= '".$codMenu."'
			");
   		if (isset($return->codMenu)) {
   			return ($return);
   		}else{
   			return(null);
   		}
    }

    /**
     * Verifica se já existe o menu
     *
     * @param integer $Menu
     * @param integer $codMenuPai
     * @return boolean
     */
    public static function existeMenu($menu,$codMenuPai) {
    	if (!$codMenuPai) $codMenuPai = 0;
		global $system;
    	$return	= $system->db->extraiPrimeiro("
				SELECT	COUNT(*) num
				FROM	MENU M
				WHERE	M.menu 					= '".$menu."'
				AND		IFNULL(M.codMenuPai,0)	= '".$codMenuPai."'
			");
   		if ((isset($return->num)) && ($return->num > 0)) {
   			return (true);
   		}else{
   			return(false);
   		}
    }

    /**
     * Resgata a ordem de um Menu
     *
     * @param integer $codMenu
     * @return array
     */
    public static function DBGetOrdemMenu($codTipoUsuario,$codMenu) {
		global $system;
    	$return	= $system->db->extraiPrimeiro("
				SELECT	MTU.ordem
				FROM	MENU_TIPO_USUARIO MTU
				WHERE	MTU.codMenu					= '".$codMenu."'
				AND		MTU.codTipoUsuario			= '".$codTipoUsuario."'
			");
   		if (isset($return->ordem)) {
   			return ($return->ordem);
   		}else{
   			return(null);
   		}
    }

    /**
     * Verifica se o menu está disponível para um tipo de Usuário
     *
     * @param integer $codMenu
     * @param integer $codTipoUsuario
     * @return boolean
     */
    public static function DBMenuEstaDisponivelTipoUsuario($codMenu,$codTipoUsuario) {
		global $system;
    	$return	= $system->db->extraiPrimeiro("
				SELECT	COUNT(*) num
				FROM	MENU_TIPO_USUARIO MTU
				WHERE	MTU.codMenu			= '".$codMenu."'
				AND		MTU.codTipoUsuario	= '".$codTipoUsuario."'
			");
   		if ((isset($return->num)) && ($return->num > 0)) {
   			return (true);
   		}else{
   			return(false);
   		}
    }


    /**
	 * Resgatar um array com a árvore completa de um menu
     */
    public static function getArrayArvoreMenu($codMenu) {
		global $system;
    	
    	$array		= array();
    	$info 		= MCMenu::DBGetInfoMenu($codMenu);
    	
    	if (!$info) return ($array);
    	$codMenuPai	= $info->codMenuPai;
    	$array[]	= $info->codMenu;
    	
    	while ($codMenuPai != '') {
    		$info		= MCMenu::DBGetInfoMenu($codMenuPai);
    		$codMenuPai	= $info->codMenuPai;
    		$array[]	= $info->codMenu;
	    	if (!$info) return (array_reverse($array));
    	}
    	
    	return (array_reverse($array));
    }
    
    /**
	 * Resgatar um array com a árvore completa de um menu com a Url
     */
    public static function getArrayArvoreMenuUrl($codMenu) {
		global $system;
    	
    	$array		= array();
    	$info 		= MCMenu::DBGetInfoMenu($codMenu);
    	
    	if (!$info) return ($array);
    	$codMenuPai				= $info->codMenuPai;
    	$array[$info->codMenu]	= $info;
    	
    	while ($codMenuPai != '') {
    		$info		= MCMenu::DBGetInfoMenu($codMenuPai);
    		$codMenuPai	= $info->codMenuPai;
    		$array[$info->codMenu]	= $info;
	    	if (!$info) return (array_reverse($array));
    	}
    	
    	return (array_reverse($array));
    }
    
    /**
	 * Resgatar um array com os dependentes de um menu
     */
    public static function getArrayDependentesMenu($codMenu,&$array) {
		global $system;
    	$dependentes	= MCMenu::DBGetDependentesMenu($codMenu);
    	for ($i = 0; $i < sizeof($dependentes); $i++) {
    		$array[]	= $dependentes[$i]->codMenu;
    		MCMenu::getArrayDependentesMenu($dependentes[$i]->codMenu,$array);
    	}
    }

    /**
     * Resgata os dependentes direto de um menu
     *
     * @param integer $codMenu
     * @return array
     */
    public static function DBGetDependentesMenu($codMenu) {
		global $system;
    	return ($system->db->extraiTodos("
				SELECT	M.*
				FROM	MENU M
				WHERE	M.codMenuPai				= '".$codMenu."'
			")
    	);
    }

    /**
	 * Associa menu a um tipo de Usuário
     */
    public static function addMenuTipoUsuario($codMenuDe,$codMenuPara,$codTipoUsuario,$codMenuPai) {
		global $system;
    	
    	/** Resgata as informações dos menus **/
    	$infoDe			= MCMenu::DBGetInfoMenu($codMenuDe);
    	$infoPara		= MCMenu::DBGetInfoMenu($codMenuPara);

    	if (!$infoDe) 	return false;
    	
    	if (!$infoPara) {
    		$dispPara	= false;
    	}else{
    		$dispPara	= true;
    	}
    	
    	/** Verifica se o menu de origem já está disponível para o usuário **/
    	$dispDe	= MCMenu::DBMenuEstaDisponivelTipoUsuario($codMenuDe,$codTipoUsuario);
    	

    	/** Verifica a ordem do menu de **/
    	$ordemDe 	= MCMenu::DBDescobreOrdemMenu($codTipoUsuario,$codMenuPai,$codMenuPara);
    	
   		if ($dispPara) {
	    	//$system->db->debug->debug("2");
   			$return = MCMenu::DBAvancaOrdemMenu($codTipoUsuario,$codMenuPai,$ordemDe);
   			if ($return) $system->halt($return);
   		}

   		/** Disponibiliza o menu para o tipo do usuário caso não esteja disponível **/
    	if (!$dispDe) {
			//$system->db->debug->debug("3");
    		$return = MCMenu::DBaddMenuTipoUsuario($codMenuDe,$codTipoUsuario,$ordemDe);
    		if ($return) $system->halt($return);
    	}else{
	    	/** Alter a ordem do menu de **/
   			$return = MCMenu::DBAlteraOrdemMenu($codTipoUsuario,$codMenuDe,$ordemDe);
   			if ($return) $system->halt($return);
    	}
    }

    /**
	 * Desassocia um menu de um tipo de Usuário
     */
    public static function delMenuTipoUsuario($codMenuDe,$codTipoUsuario,$codMenuPai) {
		global $system;
    	
    	/** Resgata as informações dos menus **/
    	$infoDe			= MCMenu::DBGetInfoMenu($codMenuDe);

    	if (!$infoDe) 	return false;
    	
    	/** Verifica se o menu de origem já está disponível para o usuário **/
    	$dispDe	= MCMenu::DBMenuEstaDisponivelTipoUsuario($codMenuDe,$codTipoUsuario);
    	
    	if (!$dispDe) return false;

    	/** Verifica a ordem do menu de **/
    	$ordem 	= MCMenu::DBGetOrdemMenu($codTipoUsuario,$codMenuDe);
    	
		$return = MCMenu::DBDiminuiOrdemMenu($codTipoUsuario,$codMenuPai,$ordem);
		if ($return) $system->halt($return);
    	
   		$return = MCMenu::DBdelMenuTipoUsuario($codMenuDe,$codTipoUsuario);
   		
   		/** Desassocia os dependentes **/
   		$dependentes	= array();
   		MCMenu::getArrayDependentesMenu($codMenuDe,$dependentes);
   		for ($i = 0; $i < sizeof($dependentes); $i++) {
   			$return = MCMenu::DBdelMenuTipoUsuario($dependentes[$i],$codTipoUsuario);
   		}
    }

    /**
	 * Associa menu a um tipo de Usuário no banco
     */
    protected function DBaddMenuTipoUsuario($codMenu,$codTipoUsuario,$ordem) {
		global $system;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO MENU_TIPO_USUARIO (codMenu, codTipoUsuario,ordem) VALUES (?,?,?)",
				array($codMenu,$codTipoUsuario,$ordem)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Desassocia menu a um tipo de Usuário no banco
     */
    protected function DBdelMenuTipoUsuario($codMenu,$codTipoUsuario) {
		global $system;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("DELETE FROM MENU_TIPO_USUARIO WHERE	codMenu = ? AND codTipoUsuario = ?",
				array($codMenu,$codTipoUsuario)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Desassocia menu de todos os tipos de usuários
     */
    protected function DBDesassociaMenu($codMenu) {
		global $system;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("DELETE FROM MENU_TIPO_USUARIO WHERE codMenu = ?",
				array($codMenu)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Exclui um Menu
     */
    protected function DBExcluiMenu($codMenu) {
		global $system;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("DELETE FROM MENU WHERE codMenu = ?",
				array($codMenu)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }
    
    /**
	 * Altera a ordem de um menu
     */
    protected function DBAlteraOrdemMenu($codTipoUsuario,$codMenu,$ordem) {
		global $system;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("UPDATE MENU_TIPO_USUARIO MTU SET MTU.ordem = ? WHERE MTU.codMenu = ? AND MTU.codTipoUsuario = ?",
				array($ordem,$codMenu,$codTipoUsuario)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Avança a ordem dos menus em 1 posicao para frente
     */
    protected function DBAvancaOrdemMenu($codTipoUsuario,$codMenuPai,$ordem) {
		global $system;
    	if ($codMenuPai == null) $codMenuPai = 0;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("UPDATE MENU_TIPO_USUARIO MTU SET MTU.ordem = MTU.ordem+1 WHERE MTU.ordem >= ? AND MTU.codMenu IN (SELECT M.codMenu FROM MENU M WHERE IFNULL(M.codMenuPai,0) = ?) AND MTU.codTipoUsuario = ?",
				array($ordem,$codMenuPai,$codTipoUsuario)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Diminui a ordem dos menus em 1 posicao
     */
    protected function DBDiminuiOrdemMenu($codTipoUsuario,$codMenuPai,$ordem) {
		global $system;
    	if ($codMenuPai == null) $codMenuPai = 0;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("UPDATE MENU_TIPO_USUARIO MTU SET MTU.ordem = MTU.ordem-1 WHERE MTU.ordem > ? AND MTU.codMenu IN (SELECT M.codMenu FROM MENU M WHERE IFNULL(M.codMenuPai,0) = ?) AND MTU.codTipoUsuario = ?",
				array($ordem,$codMenuPai,$codTipoUsuario)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Descobre a ordem de um novo menu
     */
    public static function DBDescobreOrdemMenu($codTipoUsuario,$codMenuPai,$codMenu = null) {
		global $system;
    	if ($codMenu != null) {
    		$where	= " AND M.codMenu	= '".$codMenu."'";
    	}else{
    		$where	= " ";
    	}
    	
    	if ($codMenuPai == null) {
    		$codMenuPai	= '0';
    	}
    	
    	$return	= $system->db->extraiPrimeiro("
				SELECT	IFNULL(MAX(MTU.ordem),0) ordem
				FROM	MENU_TIPO_USUARIO MTU,
						MENU M
				WHERE	M.codMenu					= MTU.codMenu
				AND		IFNULL(M.codMenuPai,'0')	= '".$codMenuPai."'
				AND		MTU.codTipoUsuario			= '".$codTipoUsuario."'
				$where
			");
   		if (isset($return->ordem)) {
   			if (($codMenu == null) || ($return->ordem == 0)) {
   				return ($return->ordem+1);
   			}else{
   				return ($return->ordem);
   			}
   		}else{
   			return(null);
   		}
    }
    
    /**
	 * Salva Informações de um Menu
     */
    public static function DBSalvaInfoMenu($codMenu,$menu,$descricao,$codTipo,$link,$nivel,$codMenuPai,$icone) {
		global $system;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("
				UPDATE	MENU M
				SET 	M.menu			= ?,
						M.descricao		= ?,
						M.codTipo		= ?,
						M.link			= ?,
						M.nivelArvore	= ?,
						M.codMenuPai	= ?,
						M.icone			= ?
				WHERE	M.codMenu 		= ?
			",
			array($menu,$descricao,$codTipo,$link,$nivel,$codMenuPai,$icone,$codMenu)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Cadastra um novo menu no banco
     */
    protected function DBCriaMenu($codMenu,$menu,$descricao,$codTipo,$link,$nivelArvore,$codMenuPai,$icone) {
		global $system;
    	try {
			$system->db->con->beginTransaction();
			$system->db->Executa("INSERT INTO MENU (codMenu,menu,descricao,codTipo,link,nivelArvore,codMenuPai,icone) VALUES (?,?,?,?,?,?,?,?)",
				array($codMenu,$menu,$descricao,$codTipo,$link,$nivelArvore,$codMenuPai,$icone)
			);
			$system->db->con->commit();
			return null;
		}catch (Exception $e) {
			$system->db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Cria um novo menu
     */
    public static function criaMenu($menu,$descricao,$codTipo,$link,$codMenuPai,$icone) {
		global $system;
		
		/**
		 * Descobre o nível da árvore através do codMenuPai
		 */
		if ($codMenuPai == '' || !$codMenuPai || $codMenuPai == 'NULL') {
			$nivelArvore	= '0';
			$codMenuPai		= null;
		}else{
			$infoPai		= MCMenu::DBGetInfoMenu($codMenuPai);
			if (!$infoPai) {
				return 'Menu Pai não encontrado';
			}
			$nivelArvore	= $infoPai->nivelArvore + 1;
		}
		
		if ($codTipo == 'M') {
			$link	= '';
		}
		
		/**
		 * Verifica se já existe menu
		 */
		if (MCMenu::existeMenu($menu,$codMenuPai) == true) {
			$system->halt('Menu já existe !!!',false,false,true);
		}else{
			$return	= MCMenu::DBCriaMenu(null,$menu,$descricao,$codTipo,$link,$nivelArvore,$codMenuPai,$icone);
			if ($return) {
				$system->halt($return);
			}
		}
    }
    
    /**
	 * Exclui um menu
     */
    public static function excluiMenu($codMenu) {
		global $system;
		
		/**
		 * Resgata o array de dependentes
		 */
		$dependentes = array();
		MCMenu::getArrayDependentesMenu($codMenu,$dependentes);
		
		/** Desassocia todos os dependentes **/
		for ($i = 0; $i < sizeof($dependentes); $i++) {
			$return = MCMenu::DBDesassociaMenu($dependentes[$i]);
			if ($return) return ($return);
		}
		
		/** Exclui todos os dependentes **/
		for ($i = 0; $i < sizeof($dependentes); $i++) {
			$return = MCMenu::DBExcluiMenu($dependentes[$i]);
			if ($return) return ($return);
		}
		
		/** Desassocia o menu **/
		$return = MCMenu::DBDesassociaMenu($codMenu);
		if ($return) return ($return);
		
		/** Exclui o menu **/
		$return = MCMenu::DBExcluiMenu($codMenu);
		if ($return) return ($return);

		return (null);
    }

    
}