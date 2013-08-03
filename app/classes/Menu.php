<?php

/**
 * Menu
 * 
 * @package: Menu
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */

class Menu {

	/**
     * Construtor
     *
	 * @return void
	 */
	private function __construct() {
		global $system,$log,$db;

		$log->debug(__CLASS__.": nova Instância");
	}
	
    /**
     * Resgata os menus por tipo de usuário
     *
     * @param integer $usuario
     * @return array
     */
    public static function DBGetMenuItens($usuario) {
		global $system,$log,$db;
    	return (
    		$db->extraiTodos("
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
		global $system,$log,$db;
    	if ($menuPai != null) {
    		$where	= " AND	M.codMenuPai	= '".$menuPai."'";
    	}else{
    		$where	= " AND	M.nivelArvore	= '0'";
    	}
    	
    	return (
    		$db->extraiTodos("
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
		global $system,$log,$db;
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
    		$db->extraiTodos("
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
		global $system,$log,$db;
    	if ($nivel !== null) {
    		$where	= "	WHERE		M.nivelArvore = '".$nivel."'";
    	}else{
    		$where	= " ";
    	}
    	
    	return (
    		$db->extraiTodos("
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
		global $system,$log,$db;
    	return (
    		$db->extraiTodos("
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
		global $system,$log,$db;
    	$return	= $db->extraiPrimeiro("
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
		global $system,$log,$db;
    	$return	= $db->extraiPrimeiro("
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
		global $system,$log,$db;
    	$return	= $db->extraiPrimeiro("
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
		global $system,$log,$db;
    	$return	= $db->extraiPrimeiro("
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
		global $system,$log,$db;
    	
    	$array		= array();
    	$info 		= \Menu::DBGetInfoMenu($codMenu);
    	
    	if (!$info) return ($array);
    	$codMenuPai	= $info->codMenuPai;
    	$array[]	= $info->codMenu;
    	
    	while ($codMenuPai != '') {
    		$info		= \Menu::DBGetInfoMenu($codMenuPai);
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
		global $system,$log,$db;
    	
    	$array		= array();
    	$info 		= \Menu::DBGetInfoMenu($codMenu);
    	
    	if (!$info) return ($array);
    	$codMenuPai				= $info->codMenuPai;
    	$array[$info->codMenu]	= $info;
    	
    	while ($codMenuPai != '') {
    		$info		= \Menu::DBGetInfoMenu($codMenuPai);
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
		global $system,$log,$db;
    	$dependentes	= \Menu::DBGetDependentesMenu($codMenu);
    	for ($i = 0; $i < sizeof($dependentes); $i++) {
    		$array[]	= $dependentes[$i]->codMenu;
    		\Menu::getArrayDependentesMenu($dependentes[$i]->codMenu,$array);
    	}
    }

    /**
     * Resgata os dependentes direto de um menu
     *
     * @param integer $codMenu
     * @return array
     */
    public static function DBGetDependentesMenu($codMenu) {
		global $system,$log,$db;
    	return ($db->extraiTodos("
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
		global $system,$log,$db;
    	
    	/** Resgata as informações dos menus **/
    	$infoDe			= \Menu::DBGetInfoMenu($codMenuDe);
    	$infoPara		= \Menu::DBGetInfoMenu($codMenuPara);

    	if (!$infoDe) 	return false;
    	
    	if (!$infoPara) {
    		$dispPara	= false;
    	}else{
    		$dispPara	= true;
    	}
    	
    	/** Verifica se o menu de origem já está disponível para o usuário **/
    	$dispDe	= \Menu::DBMenuEstaDisponivelTipoUsuario($codMenuDe,$codTipoUsuario);
    	

    	/** Verifica a ordem do menu de **/
    	$ordemDe 	= \Menu::DBDescobreOrdemMenu($codTipoUsuario,$codMenuPai,$codMenuPara);
    	
   		if ($dispPara) {
	    	//$db->debug->debug("2");
   			$return = \Menu::DBAvancaOrdemMenu($codTipoUsuario,$codMenuPai,$ordemDe);
   			if ($return) $system->halt($return);
   		}

   		/** Disponibiliza o menu para o tipo do usuário caso não esteja disponível **/
    	if (!$dispDe) {
			//$db->debug->debug("3");
    		$return = \Menu::DBaddMenuTipoUsuario($codMenuDe,$codTipoUsuario,$ordemDe);
    		if ($return) $system->halt($return);
    	}else{
	    	/** Alter a ordem do menu de **/
   			$return = \Menu::DBAlteraOrdemMenu($codTipoUsuario,$codMenuDe,$ordemDe);
   			if ($return) $system->halt($return);
    	}
    }

    /**
	 * Desassocia um menu de um tipo de Usuário
     */
    public static function delMenuTipoUsuario($codMenuDe,$codTipoUsuario,$codMenuPai) {
		global $system,$log,$db;
    	
    	/** Resgata as informações dos menus **/
    	$infoDe			= \Menu::DBGetInfoMenu($codMenuDe);

    	if (!$infoDe) 	return false;
    	
    	/** Verifica se o menu de origem já está disponível para o usuário **/
    	$dispDe	= \Menu::DBMenuEstaDisponivelTipoUsuario($codMenuDe,$codTipoUsuario);
    	
    	if (!$dispDe) return false;

    	/** Verifica a ordem do menu de **/
    	$ordem 	= \Menu::DBGetOrdemMenu($codTipoUsuario,$codMenuDe);
    	
		$return = \Menu::DBDiminuiOrdemMenu($codTipoUsuario,$codMenuPai,$ordem);
		if ($return) $system->halt($return);
    	
   		$return = \Menu::DBdelMenuTipoUsuario($codMenuDe,$codTipoUsuario);
   		
   		/** Desassocia os dependentes **/
   		$dependentes	= array();
   		\Menu::getArrayDependentesMenu($codMenuDe,$dependentes);
   		for ($i = 0; $i < sizeof($dependentes); $i++) {
   			$return = \Menu::DBdelMenuTipoUsuario($dependentes[$i],$codTipoUsuario);
   		}
    }

    /**
	 * Associa menu a um tipo de Usuário no banco
     */
    protected function DBaddMenuTipoUsuario($codMenu,$codTipoUsuario,$ordem) {
		global $system,$log,$db;
    	try {
			$db->con->beginTransaction();
			$db->Executa("INSERT INTO MENU_TIPO_USUARIO (codMenu, codTipoUsuario,ordem) VALUES (?,?,?)",
				array($codMenu,$codTipoUsuario,$ordem)
			);
			$db->con->commit();
			return null;
		}catch (Exception $e) {
			$db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Desassocia menu a um tipo de Usuário no banco
     */
    protected function DBdelMenuTipoUsuario($codMenu,$codTipoUsuario) {
		global $system,$log,$db;
    	try {
			$db->con->beginTransaction();
			$db->Executa("DELETE FROM MENU_TIPO_USUARIO WHERE	codMenu = ? AND codTipoUsuario = ?",
				array($codMenu,$codTipoUsuario)
			);
			$db->con->commit();
			return null;
		}catch (Exception $e) {
			$db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Desassocia menu de todos os tipos de usuários
     */
    protected function DBDesassociaMenu($codMenu) {
		global $system,$log,$db;
    	try {
			$db->con->beginTransaction();
			$db->Executa("DELETE FROM MENU_TIPO_USUARIO WHERE codMenu = ?",
				array($codMenu)
			);
			$db->con->commit();
			return null;
		}catch (Exception $e) {
			$db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Exclui um Menu
     */
    protected function DBExcluiMenu($codMenu) {
		global $system,$log,$db;
    	try {
			$db->con->beginTransaction();
			$db->Executa("DELETE FROM MENU WHERE codMenu = ?",
				array($codMenu)
			);
			$db->con->commit();
			return null;
		}catch (Exception $e) {
			$db->con->rollback();
			return($e->getMessage());
		}
    }
    
    /**
	 * Altera a ordem de um menu
     */
    protected function DBAlteraOrdemMenu($codTipoUsuario,$codMenu,$ordem) {
		global $system,$log,$db;
    	try {
			$db->con->beginTransaction();
			$db->Executa("UPDATE MENU_TIPO_USUARIO MTU SET MTU.ordem = ? WHERE MTU.codMenu = ? AND MTU.codTipoUsuario = ?",
				array($ordem,$codMenu,$codTipoUsuario)
			);
			$db->con->commit();
			return null;
		}catch (Exception $e) {
			$db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Avança a ordem dos menus em 1 posicao para frente
     */
    protected function DBAvancaOrdemMenu($codTipoUsuario,$codMenuPai,$ordem) {
		global $system,$log,$db;
    	if ($codMenuPai == null) $codMenuPai = 0;
    	try {
			$db->con->beginTransaction();
			$db->Executa("UPDATE MENU_TIPO_USUARIO MTU SET MTU.ordem = MTU.ordem+1 WHERE MTU.ordem >= ? AND MTU.codMenu IN (SELECT M.codMenu FROM MENU M WHERE IFNULL(M.codMenuPai,0) = ?) AND MTU.codTipoUsuario = ?",
				array($ordem,$codMenuPai,$codTipoUsuario)
			);
			$db->con->commit();
			return null;
		}catch (Exception $e) {
			$db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Diminui a ordem dos menus em 1 posicao
     */
    protected function DBDiminuiOrdemMenu($codTipoUsuario,$codMenuPai,$ordem) {
		global $system,$log,$db;
    	if ($codMenuPai == null) $codMenuPai = 0;
    	try {
			$db->con->beginTransaction();
			$db->Executa("UPDATE MENU_TIPO_USUARIO MTU SET MTU.ordem = MTU.ordem-1 WHERE MTU.ordem > ? AND MTU.codMenu IN (SELECT M.codMenu FROM MENU M WHERE IFNULL(M.codMenuPai,0) = ?) AND MTU.codTipoUsuario = ?",
				array($ordem,$codMenuPai,$codTipoUsuario)
			);
			$db->con->commit();
			return null;
		}catch (Exception $e) {
			$db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Descobre a ordem de um novo menu
     */
    public static function DBDescobreOrdemMenu($codTipoUsuario,$codMenuPai,$codMenu = null) {
		global $system,$log,$db;
    	if ($codMenu != null) {
    		$where	= " AND M.codMenu	= '".$codMenu."'";
    	}else{
    		$where	= " ";
    	}
    	
    	if ($codMenuPai == null) {
    		$codMenuPai	= '0';
    	}
    	
    	$return	= $db->extraiPrimeiro("
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
		global $system,$log,$db;
    	try {
			$db->con->beginTransaction();
			$db->Executa("
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
			$db->con->commit();
			return null;
		}catch (Exception $e) {
			$db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Cadastra um novo menu no banco
     */
    protected function DBCriaMenu($codMenu,$menu,$descricao,$codTipo,$link,$nivelArvore,$codMenuPai,$icone) {
		global $system,$log,$db;
    	try {
			$db->con->beginTransaction();
			$db->Executa("INSERT INTO MENU (codMenu,menu,descricao,codTipo,link,nivelArvore,codMenuPai,icone) VALUES (?,?,?,?,?,?,?,?)",
				array($codMenu,$menu,$descricao,$codTipo,$link,$nivelArvore,$codMenuPai,$icone)
			);
			$db->con->commit();
			return null;
		}catch (Exception $e) {
			$db->con->rollback();
			return($e->getMessage());
		}
    }

    /**
	 * Cria um novo menu
     */
    public static function criaMenu($menu,$descricao,$codTipo,$link,$codMenuPai,$icone) {
		global $system,$log,$db;
		
		/**
		 * Descobre o nível da árvore através do codMenuPai
		 */
		if ($codMenuPai == '' || !$codMenuPai || $codMenuPai == 'NULL') {
			$nivelArvore	= '0';
			$codMenuPai		= null;
		}else{
			$infoPai		= \Menu::DBGetInfoMenu($codMenuPai);
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
		if (\Menu::existeMenu($menu,$codMenuPai) == true) {
			$system->halt('Menu já existe !!!',false,false,true);
		}else{
			$return	= \Menu::DBCriaMenu(null,$menu,$descricao,$codTipo,$link,$nivelArvore,$codMenuPai,$icone);
			if ($return) {
				$system->halt($return);
			}
		}
    }
    
    /**
	 * Exclui um menu
     */
    public static function excluiMenu($codMenu) {
		global $system,$log,$db;
		
		/**
		 * Resgata o array de dependentes
		 */
		$dependentes = array();
		\Menu::getArrayDependentesMenu($codMenu,$dependentes);
		
		/** Desassocia todos os dependentes **/
		for ($i = 0; $i < sizeof($dependentes); $i++) {
			$return = \Menu::DBDesassociaMenu($dependentes[$i]);
			if ($return) return ($return);
		}
		
		/** Exclui todos os dependentes **/
		for ($i = 0; $i < sizeof($dependentes); $i++) {
			$return = \Menu::DBExcluiMenu($dependentes[$i]);
			if ($return) return ($return);
		}
		
		/** Desassocia o menu **/
		$return = \Menu::DBDesassociaMenu($codMenu);
		if ($return) return ($return);
		
		/** Exclui o menu **/
		$return = \Menu::DBExcluiMenu($codMenu);
		if ($return) return ($return);

		return (null);
    }

    
}