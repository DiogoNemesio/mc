<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

/** Definindo o timezone padrão **/
date_default_timezone_set($system->config["data"]["timezone"]);

/** Resgatar o Skin que será usado **/
//$skin	= $system->DBGetParametro('CODSKIN');

/** Resgatar as configurações do skin **/
//$system->setSkin($skin);

/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');

/** Verifica o tipo de usuário para direcionar para o menu correto **/
$info		= \Usuarios::getInfo($system->getCodUsuario());
$codTipo	= $info->codTipo;

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(HTML_PATH . 'admin.html');

/** Define os valores das variáveis **/
$template->assign('URL_FORM'	,$_SERVER['REQUEST_URI']);
$template->assign('NOME_SISTEMA', $system->config["nome"]);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>
