<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

/** Carregando o template html **/
$tpl	= new \Zage\Template();
$tpl->load(\Zage\Util::getCaminhoCorrespondente(__FILE__,\Zage\ZWS::EXT_HTML));

if (!isset($mensagem)) {
	$mensagem	= null;
}

$info = base64_encode("URL_FORM=".$_SERVER['REQUEST_URI']."&mensagem=".$mensagem);

/** Define os valores das variáveis **/
$tpl->set("INFO"				,$info);
$tpl->set('NOME_SISTEMA'		,$system->config["nome"]);
$tpl->set("MENSAGEM"			,$mensagem);
$tpl->set("ICON_IMG"			,"megaCondominio.png");
$tpl->set("SKIN"				,$system->getSkinBaseDir());
$tpl->set("SKIN_NAME" 			,$system->getSkinName());

/** Por fim exibir a página HTML **/
$tpl->show();

?>