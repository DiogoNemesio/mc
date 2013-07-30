<?php
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');

/** Resgatando valores postados **/
if (isset($_GET['id'])) {
	$id = DHCUtil::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = DHCUtil::antiInjection($_POST["id"]);
}else{
	$id = null;
}

/** Descompactar as variáveis **/
DHCUtil::descompactaId($id);

if (!isset($codCondominio)) $codCondominio = null;

/** Cria o objeto do Menu DHTMLX **/
$menu	= new DHCDHXMenu("DMenu");

/** Carrega os menus do banco **/
$menus	= MCMenu::DBGetMenuItens($system->getUsuario());

/** Adiciona os menus no objeto **/
for ($i = 0; $i < sizeof($menus); $i++) {
	$menu->addMenu($menus[$i]->codMenu,$menus[$i]->menu,$menus[$i]->descricao,$menus[$i]->codTipo,$menus[$i]->link,$menus[$i]->nivelArvore,$menus[$i]->codMenuPai,$menus[$i]->icone,$codCondominio);
}

/** Gera o código HTML e Javascript do Menu **/
$menu->render();

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));


/** Define os valores das variáveis **/
$template->assign('MENU_CODE'	,$menu->getJS());
$template->assign('XML_STRING'	,$menu->getXML());
$template->assign('URL_FORM'	,$_SERVER['REQUEST_URI']);


/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>