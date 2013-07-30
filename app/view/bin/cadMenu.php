<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');

include_once(ADM_PATH . '/security.php');


/**
 * Recuperar variáveis do form
 */
if (isset($_POST["codMenu"])) 			$codMenu			= DHCUtil::antiInjection($_POST['codMenu']);
if (isset($_POST["codTipoUsuario"])) 	$codTipoUsuario		= DHCUtil::antiInjection($_POST['codTipoUsuario']);
if (isset($_POST["acao"])) 				$acao				= DHCUtil::antiInjection($_POST['acao']);
if (isset($_POST["codMenuDe"])) 		$codMenuDe			= DHCUtil::antiInjection($_POST['codMenuDe']);
if (isset($_POST["codMenuPara"])) 		$codMenuPara		= DHCUtil::antiInjection($_POST['codMenuPara']);
if (isset($_POST["sMenu"])) 			$sMenu				= DHCUtil::antiInjection($_POST['sMenu']);
if (isset($_POST["sDescricao"])) 		$sDescricao			= DHCUtil::antiInjection($_POST['sDescricao']);
if (isset($_POST["sLink"])) 			$sLink				= DHCUtil::antiInjection($_POST['sLink']);
if (isset($_POST["sIcone"])) 			$sIcone				= DHCUtil::antiInjection($_POST['sIcone']);
if (isset($_POST["aMenu"])) 			$aMenu				= DHCUtil::antiInjection($_POST['aMenu']);
if (isset($_POST["aDescricao"])) 		$aDescricao			= DHCUtil::antiInjection($_POST['aDescricao']);
if (isset($_POST["aCodTipo"])) 			$aCodTipo			= DHCUtil::antiInjection($_POST['aCodTipo']);
if (isset($_POST["aIcone"])) 			$aIcone				= DHCUtil::antiInjection($_POST['aIcone']);
if (isset($_POST["codMenuDel"])) 		$codMenuDel			= DHCUtil::antiInjection($_POST['codMenuDel']);

if (isset($_POST["aLink"])) 			{
	$aLink		= DHCUtil::antiInjection($_POST['aLink']);
}else{
	$aLink		= null;
}

if (!isset($codMenu))					$codMenu		= null;
if (!isset($codTipoUsuario))			$codTipoUsuario	= $system->DBGetTipoUsuario($system->getUsuario());


/**
 * Verificar a acao
 */
if (isset($acao)) {
	
	if ($acao == 'associar') {
		MCMenu::addMenuTipoUsuario($codMenuDe,$codMenuPara,$codTipoUsuario,$codMenu);
	}elseif ($acao == 'desassociar') {
		MCMenu::delMenuTipoUsuario($codMenuDe,$codTipoUsuario,$codMenu);
	}elseif ($acao == 'salvar') {
		/**
		 * Resgatar as informações complementares
		 */
		if ((isset($codMenu)) && ($codMenu != '')) {
			$infoMenu 	= MCMenu::DBGetInfoMenu($codMenu);
			$return		= MCMenu::DBSalvaInfoMenu($codMenu,$sMenu,$sDescricao,$infoMenu->codTipo,$sLink,$infoMenu->nivelArvore,$infoMenu->codMenuPai,$sIcone);
			if ($return) $system->halt($return);
		}
	}elseif ($acao == 'criar') {
		/**
		 * Faz validação dos campos
		 */
		$valido	= true;
		if (!$aMenu) {
			$aviso	= 'Campo: "Menu" obrigatório !!!';
			$valido	= false;
		}

		if (!$aDescricao) {
			$aviso	= 'Campo: "Descrição" obrigatório !!!';
			$valido	= false;
		}

		if ($valido) {
			$system->log->debug->debug('MenuPai = '.$codMenu);
			$return = MCMenu::criaMenu($aMenu,$aDescricao,$aCodTipo,$aLink,$codMenu,$aIcone);
			if ($return) $system->halt($return);
		}else{
			$system->halt($aviso,false,false,true);
		}
	}elseif ($acao == 'excluir') {
		if (isset($codMenuDel)) {
			$return = MCMenu::excluiMenu($codMenuDel);
			if ($return) $system->halt($return);
		}
	}
}

if ((isset($codMenu) && ($codMenu != ''))) {
	$infoMenu	= MCMenu::DBGetInfoMenu($codMenu);
	if (isset($infoMenu->codTipo)) {
		$sMenu	= $infoMenu->menu;
		$sDesc	= $infoMenu->descricao;
		$sLink	= $infoMenu->link;
		$sIcone	= $infoMenu->icone;

		$hidden		= ' ';
	
		if ($infoMenu->codTipo == 'L') {
			$hidLink	= ' ';
			$hidCad		= 'MCHidden';
		}else{
			$hidLink	= 'MCHidden';
			$hidCad		= ' ';
		}
			
	}else{
		echo "OI";
		$hidden		= ' ';
		$hidLink	= ' ';
		$hidCad		= ' ';
		$sMenu		= '';
		$sDesc		= '';
		$sLink		= '';
		$sIcone		= '';
	}
	
}else{
	$hidden		= 'MCHidden';
	$hidLink	= 'MCHidden';
	$hidCad		= ' ';
	$sMenu		= '';
	$sDesc		= '';
	$sLink		= '';
	$sIcone		= '';
}


/**
 * Monta os dados do select Tipo de Usuário
 */
$tiposUsuario	= $system->DBGetListTipoUsuario();
$selTipoUsuario	= '';
for ($i = 0; $i < sizeof($tiposUsuario); $i++) {
	$selected = ($codTipoUsuario == $tiposUsuario[$i]->codTipo) ? ' selected ' : ' ';
	$selTipoUsuario	.= "<option $selected value='".$tiposUsuario[$i]->codTipo."'>".$tiposUsuario[$i]->codTipo.' - '.$tiposUsuario[$i]->descricao."</option>";
}

/**
 * Monta os dados do select Tipo de de Menu
 */
$tiposMenu	= MCMenu::DBGetListTipoMenu();
$selTipoMenu	= '';
for ($i = 0; $i < sizeof($tiposMenu); $i++) {
	$selTipoMenu	.= "<option value='".$tiposMenu[$i]->codTipo."'>".$tiposMenu[$i]->codTipo.' - '.$tiposMenu[$i]->descricao."</option>";
}

$aLocal		= MCMenu::getArrayArvoreMenu($codMenu);
//$local		= "<a href='javascript:changeLocal(\"\",\"".$codTipoUsuario."\");'>Menu Raiz</a>";;
$local		= "<input type='button' class='MCObject' value='Menu Raiz' onclick='javascript:changeLocal(\"\",\"".$codTipoUsuario."\");'>";
for ($i = 0; $i < sizeof($aLocal); $i++) {
	$info	= MCMenu::DBGetInfoMenu($aLocal[$i]);
	//$local	.= " -> <a href='javascript:changeLocal(\"".$info->codMenu."\",\"".$codTipoUsuario."\");'>".$info->menu."</a>";
	$local	.= " -> <input type='button' class='MCObject' value='".$info->menu."' onclick='javascript:changeLocal(\"".$info->codMenu."\",\"".$codTipoUsuario."\");'>";
}

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));


/** Define os valores das variáveis **/
$template->assign('FORM_ACTION'		,$_SERVER['REQUEST_URI']);
$template->assign('SEL_TIPO_USUARIO',$selTipoUsuario);
$template->assign('SEL_TIPO_MENU'	,$selTipoMenu);
$template->assign('COD_MENU_PAI'	,$codMenu);
$template->assign('COD_MENU'		,$codMenu);
$template->assign('COD_TIPO_USUARIO',$codTipoUsuario);
$template->assign('LOCALIZACAO'		,$local);
$template->assign('HIDDEN'			,$hidden);
$template->assign('HIDDEN_LINK'		,$hidLink);
$template->assign('HIDDEN_CAD'		,$hidCad);
$template->assign('SMENU'			,$sMenu);
$template->assign('SDESCRICAO'		,$sDesc);
$template->assign('SLINK'			,$sLink);
$template->assign('SICONE'			,$sIcone);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>