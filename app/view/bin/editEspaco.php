<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');


if (isset($_GET['id'])){
	$id	= DHCUtil::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])){
	$id	= DHCUtil::antiInjection($_POST["id"]);
}else{
	DHCErro::halt('Falta de Parâmetros');
}

if (isset($_GET['err'])){
	$err	= DHCUtil::antiInjection($_GET["err"]);
}else{
	$err 	= '';
}

/** Descompactar as variáveis **/
DHCUtil::descompactaId($id);

/************* Restagar o condominio **************/
if (!isset($codCondominio)){
	DHCErro::halt('Falta de Parâmetros 2');
}

/**** Resgatar dados do banco ****/
if ((!isset($codEspaco)) || (!$codEspaco)) {
	$codEspaco		= null;
	$nome			= null;
	$descricao 		= null;
	$tempoMaximo	= null;
	$indConfirmacao	= null;
	$valor			= null;
}else{
	$info 			= MCEspaco::getInfo($codEspaco);
	$nome			= $info->nome;
	$descricao 		= $info->descricao;
	$tempoMaximo	= $info->tempoMaximo;
	$indConfirmacao	= $info->indConfirmacao;
	$valor			= $info->valor;
}

/** Carregar arquivo XML do form **/
$xmlData	= DHCUtil::getXmlData(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

/** Aplica a mascara nas variáveis **/
$system->mask->aplicaMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(HTML_PATH . $system->config->defFormHtml);

/** Montar url para o botão voltar **/
$idVoltar	= DHCUtil::encodeUrl('codEspaco='.$codEspaco.'&codCondominio='.$codCondominio); 

/** Define os valores das variáveis **/
$template->assign('XML_DATA'		,$xmlData);
$template->assign('COMBOS'			,null);
$template->assign('URL_DP'			,MegaCondominio::getCaminhoCorrespondente(__FILE__, 'dp',MC_URL));
$template->assign('URL_FORM'		,$_SERVER["REQUEST_URI"]);
$template->assign('COD_ESPACO'		,$codEspaco);
$template->assign('NOME'			,$nome);
$template->assign('DESCRICAO'		,$descricao);
$template->assign('TEMPOMAXIMO'		,$tempoMaximo);
$template->assign('INDCONFIRMACAO'	,$indConfirmacao);
$template->assign('VALOR'			,$valor);
$template->assign('ID'				,$id);
$template->assign('FORM_ALTURA'		,120);
$template->assign('FORM_LARGURA'	,720);
$template->assign('MENSAGEM'		,null);

$template->assign('VOLTAR'			,BIN_URL.'cadEspaco.php?id='.$idVoltar);


/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>