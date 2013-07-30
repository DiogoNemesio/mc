<?php 
#################################################################################
## Includes
#################################################################################
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
## Resgata a variável ID que está criptografada
#################################################################################
if (isset($_GET['id'])) {
	$id = DHCUtil::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = DHCUtil::antiInjection($_POST["id"]);
}else{
	DHCErro::halt('Falta de Parâmetros');
}

#################################################################################
## Resgata as variáveis postadas 
#################################################################################
if (isset($_POST['codUnidade'])) 	$codUnidade	= DHCUtil::antiInjection($_POST["codUnidade"]);
if (isset($_POST['codEspaco'])) 	$codEspaco 	= DHCUtil::antiInjection($_POST["codEspaco"]);

#################################################################################
## Descompacta o ID
#################################################################################
DHCUtil::descompactaId($id);

#################################################################################
## Verifica se algumas variáveis estão OK
#################################################################################
if (!isset($codCondominio)) {
	echo "<script>alert('Erro variável codCondominio perdida !!!!');</script>";
	DHCErro::halt('Falta de Parâmetros (COD_CONDOMINIO)');
}


#################################################################################
## Decidir qual tela será mostrada
#################################################################################
if (isset($codEspaco) && isset($codUnidade)) {
	$template	= new DHCHtmlTemplate();
	$template->loadTemplate(HTML_PATH . $system->config->defSchedHtml);
	echo $template->getHtmlCode();
	
}else{
	
	#################################################################################
	## Verificar os dados postados
	#################################################################################
	if ((!isset($codReserva)) || (!$codReserva)) {
		$codReserva		= null;
		$codUnidade		= null;
		$codEspaco		= null;
	}else{
		$info 			= MCReserva::getInfo($codReserva);
		$codUnidade		= $info->codUnidade;
		$codEspaco		= $info->codEspaco;
	}
	
	#################################################################################
	## Carregar arquivo XML do form
	#################################################################################
	$xmlData	= DHCUtil::getXmlData(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));
	
	#################################################################################
	## Aplica a mascara nas variáveis
	#################################################################################
	$system->mask->aplicaMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));
	
	#################################################################################
	## Carregando o template html
	#################################################################################
	$template	= new DHCHtmlTemplate();
	$template->loadTemplate(HTML_PATH . $system->config->defFormHtml);
	
	#################################################################################
	## Montar url para o botão voltar
	#################################################################################
	$idVoltar	= DHCUtil::encodeUrl('codCondominio='.$codCondominio); 
	
	#################################################################################
	## Resgatar as unidades
	#################################################################################
	$oUnidades	= MegaCondominio::geraXmlCombo(MCUnidade::lista($codCondominio), 'codUnidade', 'nome', $codUnidade, null);
	
	#################################################################################
	## Resgatar os Espaços
	#################################################################################
	$oEspacos	= MegaCondominio::geraXmlCombo(MCEspaco::lista($codCondominio), 'codEspaco', 'nome', $codEspaco, null);
	
	#################################################################################
	## Define os valores das comboboxes
	#################################################################################
	$combos		= "
	var dhxComboUnidade	= dhxForm.getCombo(\"codUnidade\");
	var dhxComboEspaco	= dhxForm.getCombo(\"codEspaco\");
	dhxComboUnidade.loadXMLString('%UNIDADES%');
	dhxComboEspaco.loadXMLString('%ESPACOS%');
	dhxComboUnidade.readonly(true);
	dhxComboEspaco.readonly(true);
	dhxComboUnidade.enableOptionAutoWidth(true);
	dhxComboEspaco.enableOptionAutoWidth(true);
	";
	
	#################################################################################
	## Define os valores das variáveis
	#################################################################################
	$template->assign('XML_DATA'		,$xmlData);
	$template->assign('COMBOS'			,$combos);
	$template->assign('URL_DP'			,MegaCondominio::getCaminhoCorrespondente(__FILE__, 'dp',MC_URL));
	$template->assign('URL_FORM'		,$_SERVER["PHP_SELF"]);
	$template->assign('COD_RESERVA'		,$codReserva);
	$template->assign('COD_UNIDADE'		,$codUnidade);
	$template->assign('COD_ESPACO'		,$codEspaco);
	$template->assign('ID'				,$id);
	$template->assign('MENSAGEM'		,null);
	$template->assign('UNIDADES'		,$oUnidades);
	$template->assign('ESPACOS'			,$oEspacos);
	$template->assign('FORM_ALTURA'		,120);
	$template->assign('FORM_LARGURA'	,720);
	$template->assign('VOLTAR'			,BIN_URL.'cadReserva.php?id='.$idVoltar);
	
	
	#################################################################################
	## Por fim exibir a página HTML
	#################################################################################
	echo $template->getHtmlCode();
	






}


	
?>