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
	$id	= null;
}

/** Descompactar as variáveis **/
DHCUtil::descompactaId($id);

if (!isset($tab)) 			$tab 			= 'tCad';
if (!isset($codCondominio))	$codCondominio	= null;

/************************** Localização **************************/
$local		= MegaCondominio::geraLocalizacao($_codMenu_, $system->getTipoUsuario());

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('URL_FORM'		,$_SERVER['REQUEST_URI']);
$template->assign('TAB'				,$tab);
$template->assign('COD_CONDOMINIO'	,$codCondominio);
$template->assign('ID'				,$id);
$template->assign('LOCAL'			,$local);
$template->assign('MENSAGEM'		,null);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>