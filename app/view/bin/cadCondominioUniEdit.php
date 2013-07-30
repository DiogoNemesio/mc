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
if ((!isset($codUnidade)) || (!$codUnidade)) {
	$codUnidade		= null;
	$codBloco		= null;
	$nome	 		= null;
	$codTipo		= null;
	$codResponsavel	= null;
	$fone			= null;
	$celular		= null;
	$codVencimento	= null;
	$ramal			= null;
}else{
	$info 			= MCUnidade::getInfo($codUnidade);
	$codBloco		= $info->codBloco;
	$nome	 		= $info->nome;
	$codTipo		= $info->codTipo;
	$codResponsavel	= $info->codResponsavel;
	$fone			= $info->fone;
	$celular		= $info->celular;
	$codVencimento	= $info->codVencimento;
	$ramal			= $info->ramal;
}


/********* Resgatar os responsáveis **********/
$resps	= MCCondominio::listaUsuarios($codCondominio);
$oResps	= MegaCondominio::geraXmlCombo($resps, 'codUsuario', 'nome', $codResponsavel, '* Sem Responsável');


/********* Resgatar os Blocos **********/
$blocos		= MCCondominio::listaBlocos($codCondominio);
$oBlocos	= MegaCondominio::geraXmlCombo($blocos, 'codBloco', 'nomeBloco', $codBloco, null);

/********* Resgatar os Tipos de Unidade **********/
$tipos	= MCUnidade::listaTipos();
$oTipos	= MegaCondominio::geraXmlCombo($tipos, 'codTipo', 'descricao', $codTipo, null);

/********* Resgatar os Vencimentos **********/
$vencs	= MCVencCondominio::lista($codCondominio);
$oVencs	= MegaCondominio::geraXmlCombo($vencs, 'codVencimento', 'dia', $codVencimento, '* Sem Vencimento');


/** Carregar arquivo XML do form **/
$xmlData	= DHCUtil::getXmlData(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

/** Aplica a mascara nas variáveis **/
$system->mask->aplicaMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));

/** Montar url para o botão voltar **/
$idVoltar	= base64_encode('codBloco='.$codBloco.'&codCondominio='.$codCondominio.'&codUnidade='.$codUnidade); 

/** Define os valores das variáveis **/
$template->assign('RESPONSAVEIS'	,$oResps);
$template->assign('TIPOS'			,$oTipos);
$template->assign('BLOCOS'			,$oBlocos);
$template->assign('VENCIMENTOS'		,$oVencs);
$template->assign('XML_DATA'		,$xmlData);
$template->assign('URL_FORM'		,MegaCondominio::getCaminhoCorrespondente(__FILE__, 'dp',MC_URL));
$template->assign('NOME'			,$nome);
$template->assign('COD_UNIDADE'		,$codUnidade);
$template->assign('FONE'			,$fone);
$template->assign('CELULAR'			,$celular);
$template->assign('RAMAL'			,$ramal);
$template->assign('ID'				,$id);
$template->assign('MENSAGEM'		,null);
$template->assign('VOLTAR'			,BIN_URL.'cadCondominioUni.php?id='.$idVoltar);


/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>