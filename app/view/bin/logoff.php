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
## Resgatando valores postados
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\Util::antiInjection($_POST["id"]);
}else{
	$id = null;
}

#################################################################################
## Descompactar as variáveis
#################################################################################
\Zage\Util::descompactaId($id);


#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\Template();
$tpl->load(\Zage\Util::getCaminhoCorrespondente(__FILE__, \Zage\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('MENSAGEM'	,'Obrigado por utilizar o sistema !!!');
$tpl->set('URL_FORM'	,ROOT_URL);
$tpl->set('NOME_SISTEMA',$system->config["nome"]);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

session_unset();
session_destroy();
unset($system);
