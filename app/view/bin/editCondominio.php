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


if (!isset($tab)) 			$tab 			= 'tCad';
if (!isset($codCondominio))	$codCondominio	= null;


#################################################################################
## Gera a localização (breadcrumb)
#################################################################################
$local          = $system->geraLocalizacao($_codMenu_, $system->usuario->getTipo());

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\Template();
$tpl->load(\Zage\Util::getCaminhoCorrespondente(__FILE__, \Zage\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('URL_FORM'		,$_SERVER['REQUEST_URI']);
$tpl->set('TAB'				,$tab);
$tpl->set('COD_CONDOMINIO'	,$codCondominio);
$tpl->set('ID'				,$id);
$tpl->set('LOCALIZACAO'		,$local);
$tpl->set('MENSAGEM'		,null);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

?>