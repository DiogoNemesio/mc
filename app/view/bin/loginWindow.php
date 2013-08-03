<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

/** Carregando o template html **/
$tpl	= new \Zage\Template();
$tpl->load(\Zage\Util::getCaminhoCorrespondente(__FILE__, \Zage\ZWS::EXT_HTML));

if (!isset($mensagem)) {
	$mensagem	= null;
}


if (isset($_GET['info'])) {
	$info = \Zage\Util::antiInjection($_GET["info"]);
}else{
	\Zage\Erro::halt('Falta de Parâmetros (LoginWindow)');
}


/** Define os valores das variáveis **/
$tpl->set('INFO'			,$info);
$tpl->set('NOME_SISTEMA'	,$system->config["nome"]);
$tpl->set('MENSAGEM'		,$mensagem);
$tpl->set('SB_MESSAGE'		,"Megacondomínio todos os direitos reservados");
$tpl->set('HTMLX_IMG_URL'	,HTMLX_IMG_URL);
$tpl->set('SKIN'			,$system->getSkin());
$tpl->set('SKIN_NAME'		,$system->getSkinName());

/** Por fim exibir a página HTML **/
$tpl->show();

?>