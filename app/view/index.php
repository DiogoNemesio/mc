<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

/** Definindo o timezone padrão **/
date_default_timezone_set($system->config["data"]["timezone"]);

/** Resgatar o Skin que será usado **/
$skin	= $system->DBGetParametro('CODSKIN');

/** Resgatar as configurações do skin **/
$system->setSkin($skin);

/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');

/** Verifica o tipo de usuário para direcionar para o menu correto **/
$info		= \Usuarios::getInfo($system->getCodUsuario());
$codTipo	= $info->codTipo;

/** Carregando o template html **/
$tpl	= new \Zage\Template();
$tpl->load(HTML_PATH . 'index.html');

/** Define os valores das variáveis **/
$tpl->set('URL_FORM'		,$_SERVER['REQUEST_URI']);
$tpl->set('NOME_SISTEMA'	, $system->config["nome"]);
$tpl->set('ICON_IMG'		,"megaCondominio.png");
$tpl->set('SKIN'			,$system->getSkin());
$tpl->set('SKIN_NAME'		,$system->getSkinName());


/** Por fim exibir a página HTML **/
$tpl->show();

?>
