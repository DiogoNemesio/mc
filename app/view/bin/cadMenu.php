<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
#################################################################################
## Verifica se o usuário está autenticado
#################################################################################
include_once(BIN_PATH . 'auth.php');
include_once(DOC_ROOT . '/security.php');

#################################################################################
## Resgata os parâmetros passados pelo menu
#################################################################################
if (isset($_GET["id"])) 	{
	$id = \Zage\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST["id"])) 	{
	$id = \Zage\Util::antiInjection($_POST["id"]);
}elseif (isset($id)) 	{
	$id = \Zage\Util::antiInjection($id);
}else{
	die('Parâmetro inválido 1');
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\Util::descompactaId($id);


#################################################################################
##  Recuperar variáveis do form
#################################################################################
if (isset($_POST["codMenu"])) 			$codMenu			= \Zage\Util::antiInjection($_POST['codMenu']);
if (isset($_POST["codTipoUsuario"])) 	$codTipoUsuario		= \Zage\Util::antiInjection($_POST['codTipoUsuario']);
if (isset($_POST["acao"])) 				$acao				= \Zage\Util::antiInjection($_POST['acao']);
if (isset($_POST["codMenuDe"])) 		$codMenuDe			= \Zage\Util::antiInjection($_POST['codMenuDe']);
if (isset($_POST["codMenuPara"])) 		$codMenuPara		= \Zage\Util::antiInjection($_POST['codMenuPara']);
if (isset($_POST["sMenu"])) 			$sMenu				= \Zage\Util::antiInjection($_POST['sMenu']);
if (isset($_POST["sDescricao"])) 		$sDescricao			= \Zage\Util::antiInjection($_POST['sDescricao']);
if (isset($_POST["sLink"])) 			$sLink				= \Zage\Util::antiInjection($_POST['sLink']);
if (isset($_POST["sIcone"])) 			$sIcone				= \Zage\Util::antiInjection($_POST['sIcone']);
if (isset($_POST["aMenu"])) 			$aMenu				= \Zage\Util::antiInjection($_POST['aMenu']);
if (isset($_POST["aDescricao"])) 		$aDescricao			= \Zage\Util::antiInjection($_POST['aDescricao']);
if (isset($_POST["aCodTipo"])) 			$aCodTipo			= \Zage\Util::antiInjection($_POST['aCodTipo']);
if (isset($_POST["aIcone"])) 			$aIcone				= \Zage\Util::antiInjection($_POST['aIcone']);
if (isset($_POST["codMenuDel"])) 		$codMenuDel			= \Zage\Util::antiInjection($_POST['codMenuDel']);

if (isset($_POST["aLink"])) 			{
	$aLink		= \Zage\Util::antiInjection($_POST['aLink']);
}else{
	$aLink		= null;
}

if (!isset($codMenu))					$codMenu		= null;
if (!isset($codTipoUsuario))			$codTipoUsuario	= $system->DBGetTipoUsuario($system->getUsuario());

################################################################################
## Verificar a acao
#################################################################################
if (isset($acao)) {
	
	if ($acao == 'associar') {
		\Zage\Menu::addMenuTipoUsuario($codMenuDe,$codMenuPara,$codTipoUsuario,$codMenu);
	}elseif ($acao == 'desassociar') {
		\Zage\Menu::delMenuTipoUsuario($codMenuDe,$codTipoUsuario,$codMenu);
	}elseif ($acao == 'salvar') {
		#################################################################################
		## Resgatar as informações complementares
		#################################################################################
		if ((isset($codMenu)) && ($codMenu != '')) {
			$infoMenu 	= \Zage\Menu::DBGetInfoMenu($codMenu);
			$return		= \Zage\Menu::DBSalvaInfoMenu($codMenu,$sMenu,$sDescricao,$infoMenu->codTipo,$sLink,$infoMenu->nivelArvore,$infoMenu->codMenuPai,$sIcone);
			if ($return) $system->halt($return);
		}
	}elseif ($acao == 'criar') {
		#################################################################################
		## Faz validação dos campos
		#################################################################################
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
			$return = \Zage\Menu::criaMenu($aMenu,$aDescricao,$aCodTipo,$aLink,$codMenu,$aIcone);
			if ($return) $system->halt($return);
		}else{
			$system->halt($aviso,false,false,true);
		}
	}elseif ($acao == 'excluir') {
		if (isset($codMenuDel)) {
			$return = \Zage\Menu::excluiMenu($codMenuDel);
			if ($return) $system->halt($return);
		}
	}
}

if ((isset($codMenu) && ($codMenu != ''))) {
	$infoMenu	= \Zage\Menu::DBGetInfoMenu($codMenu);
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


#################################################################################
## Monta os dados do select Tipo de Usuário
#################################################################################
$tiposUsuario	= $system->DBGetListTipoUsuario();
$selTipoUsuario	= '';
foreach ($tiposUsuario as $dados) {
	$selected = ($codTipoUsuario == $dados->codTipo) ? ' selected ' : ' ';
	$selTipoUsuario	.= "<option $selected value='".$dados->codTipo."'>".$dados->codTipo.' - '.$dados->descricao."</option>";
}

#################################################################################
## Monta os dados do select Tipo de de Menu
#################################################################################
$tiposMenu	= \Menu::DBGetListTipoMenu();
$selTipoMenu	= '';
foreach ($tiposMenu as $dados) {
	$selTipoMenu	.= "<option value='".$dados->codTipo."'>".$dados->codTipo.' - '.$dados->descricao."</option>";
}

$aLocal		= \Menu::getArrayArvoreMenu($codMenu);
$local		= "<input type='button' class='MCObject' value='Menu Raiz' onclick='javascript:changeLocal(\"\",\"".$codTipoUsuario."\");'>";
for ($i = 0; $i < sizeof($aLocal); $i++) {
	$info	= \Menu::DBGetInfoMenu($aLocal[$i]);
	$local	.= " -> <input type='button' class='MCObject' value='".$info->menu."' onclick='javascript:changeLocal(\"".$info->codMenu."\",\"".$codTipoUsuario."\");'>";
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\Template();
$tpl->load(\Zage\Util::getCaminhoCorrespondente(__FILE__, \Zage\ZWS::EXT_HTML));


#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('FORM_ACTION'		,$_SERVER['REQUEST_URI']);
$tpl->set('SEL_TIPO_USUARIO',$selTipoUsuario);
$tpl->set('SEL_TIPO_MENU'	,$selTipoMenu);
$tpl->set('COD_MENU_PAI'	,$codMenu);
$tpl->set('COD_MENU'		,$codMenu);
$tpl->set('COD_TIPO_USUARIO',$codTipoUsuario);
$tpl->set('LOCALIZACAO'		,$local);
$tpl->set('HIDDEN'			,$hidden);
$tpl->set('HIDDEN_LINK'		,$hidLink);
$tpl->set('HIDDEN_CAD'		,$hidCad);
$tpl->set('SMENU'			,$sMenu);
$tpl->set('SDESCRICAO'		,$sDesc);
$tpl->set('SLINK'			,$sLink);
$tpl->set('SICONE'			,$sIcone);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

?>