<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');


if ($system->ehAdmin($system->getUsuario()) == false) {
	$RO	= 'readonly';
}else{
	$RO	= '';
}

$codEstado	= 'AL';
$codCidade	= '';


/**************** Verificar se existe algum condomínio associado ****************/
if (isset($_GET['tab']))			$tab			= DHCUtil::antiInjection($_GET["tab"]);
if (isset($_GET['codCondominio']))	$codCondominio	= DHCUtil::antiInjection($_GET["codCondominio"]);

//print_r($codCondominio);

if (!$codCondominio) {
	echo "<script> alert('Não existe condomínio cadastrado');</script>";
	exit();
}	
	
/** Resgatar os Estados **/
$aEstados = $system->DBGetEstados();
$oEstados = '';
//$oEstados .= '<option value="">--</option>';
for($i=0; $i<sizeof($aEstados);$i++) {
	$selected = ($codEstado == $aEstados[$i]->codEstado) ? ' selected ' : ' ';
	$oEstados .= "<option $selected img_src='".BANDEIRAS_URL.$aEstados[$i]->bandeira."' value='".$aEstados[$i]->codEstado."'>".$aEstados[$i]->codEstado. ' - '.$aEstados[$i]->nomeEstado.'</option>';
}

/** Resgatar as cidades **/
if (isset($codEstado)) {
	$aCidades 	= $system->DBGetCidades($codEstado);
	$oCidades	= '';
	for($i=0; $i<sizeof($aCidades);$i++) {
		$selected = ($codCidade == $aCidades[$i]->codCidade) ? ' selected ' : ' ';
		$oCidades .= "<option $selected value='".$aCidades[$i]->codCidade."'>".$aCidades[$i]->nomeCidade.'</option>';
	}
}

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));


/** Define os valores das variáveis **/
$template->assign('URL_FORM'		,$_SERVER['REQUEST_URI']);
$template->assign('ESTADOS'			,$oEstados);
$template->assign('CONDOMINIOS_URL'	,CONDOMINIOS_URL);
$template->assign('RO'				,$RO);



/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>