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

/*************Restagar o condominio**************/
if (!isset($codCondominio)){
	DHCErro::halt('Falta de Parâmetros 2');
}

if (!isset($codBloco)) {
	$codBloco	= null;
	$nomeBloco 	= null;
	$descricao 	= null;
	$codSindico = null;
}else{
	$info 		= MCBloco::getInfo($codBloco);
	$nomeBloco 	= $info->nomeBloco;
	$descricao 	= $info->descricao;
	$codSindico = $info->codSindico;
}

/********* Resgatar os síndicos **********/
$sindicos	= MCUsuarios::listaSindicos();
$oSindicos	= MegaCondominio::geraXmlCombo($sindicos, 'codUsuario', 'nome', $codSindico, '* Sem Síndico');

/** Carregar arquivo XML do form **/
$xmlData	= DHCUtil::getXmlData(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));


/** Montar url para o botão voltar **/
$idVoltar	= base64_encode('codBloco='.$codBloco.'&codCondominio='.$codCondominio); 

/** Url botão voltar **/
$urlVoltar	= BIN_URL . "cadCondominioBlo.php?id=".$idVoltar;

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Define os valores das variáveis **/
$template->assign('XML_DATA'		,$xmlData);
$template->assign('SINDICOS'		,$oSindicos);
$template->assign('URL_FORM'		,MegaCondominio::getCaminhoCorrespondente(__FILE__, 'dp',MC_URL));
$template->assign('MENSAGEM'		,null);
$template->assign('ID'				,$id);
$template->assign('COD_BLOCO'		,$codBloco);
$template->assign('NOME'			,$nomeBloco);
$template->assign('DESCRICAO'		,$descricao);
$template->assign('VOLTAR'			,$urlVoltar);


/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>