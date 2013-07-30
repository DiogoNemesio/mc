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

if (!isset($codUsuario))	$codUsuario 	= null;

/** Verifica se o usuário é administrador para mostrar a aba de associação com o condomínio **/
if ($system->ehAdmin($system->getUsuario()) == false) {
	$mostraAba	= false;
}else{
	$mostraAba	= "1";
}

/************************** Localização **************************/
$local		= MegaCondominio::geraLocalizacao($_codMenu_, $system->getTipoUsuario());

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('URL_FORM'		,$_SERVER['REQUEST_URI']);
$template->assign('COD_USUARIO'		,$codUsuario);
$template->assign('MOSTRAABA'		,$mostraAba);
$template->assign('ID'				,$id);
$template->assign('LOCAL'			,$local);
$template->assign('MENSAGEM'		,null);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>