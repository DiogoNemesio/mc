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
if ((!isset($codMorador)) || (!$codMorador)) {
	$codMorador		= null;
	$codUnidade		= null;
	$nome	 		= null;
	$fone			= null;
	$codTipoSexo	= null;
	$codUsuario		= null;
}else{
	$info 			= MCMorador::getInfo($codMorador);
	$codUnidade		= $info->codUnidade;
	$nome	 		= $info->nome;
	$fone			= $info->fone;
	$codTipoSexo	= $info->codTipoSexo;
	$codUsuario		= $info->codUsuario;
}


/********* Resgatar as unidades **********/
$unidades	= MCUnidade::lista($codCondominio);
$oUnidades	= MegaCondominio::geraXmlCombo($unidades, 'codUnidade', 'nome', $codUnidade, null);

/********* Resgatar os sexos **********/
$sexos	= MCMorador::listaSexos();
$oSexos	= MegaCondominio::geraXmlCombo($sexos, 'codTipo', 'descricao', $codTipoSexo, null);

/********* Resgatar os usuários do condominio **********/
$usuarios	= MCCondominio::listaUsuarios($codCondominio);
$oUsuarios	= MegaCondominio::geraXmlCombo($usuarios, 'codUsuario', 'nome', $codUsuario, null);

/** Carregar arquivo XML do form **/
$xmlData	= DHCUtil::getXmlData(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

/** Aplica a mascara nas variáveis **/
$system->mask->aplicaMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Montar url para o botão voltar **/
$idVoltar	= DHCUtil::encodeUrl('codMorador='.$codMorador.'&codCondominio='.$codCondominio); 

/** Define os valores das variáveis **/
$template->assign('UNIDADES'		,$oUnidades);
$template->assign('SEXO'			,$oSexos);
$template->assign('XML_DATA'		,$xmlData);
$template->assign('URL_FORM'		,MegaCondominio::getCaminhoCorrespondente(__FILE__, 'dp',MC_URL));
$template->assign('COD_MORADOR'		,$codMorador);
$template->assign('NOME'			,$nome);
$template->assign('FONE'			,$fone);
$template->assign('ID'				,$id);
$template->assign('MENSAGEM'		,null);
$template->assign('VOLTAR'			,BIN_URL.'cadMorador.php?id='.$idVoltar);


/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>