<?php
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');

//$system->log->debug->debug('_GET: '.serialize($_GET));
//$system->log->debug->debug('_POST: '.serialize($_POST));

/** Resgatando valores postados **/
if (isset($_GET['id'])) {
	$id = DHCUtil::antiInjection($_GET["id"]);
}else{
	echo "Requisição inválida !!";
	exit;
}

/** Descompactar as variáveis **/
DHCUtil::descompactaId($id);

/************************** Resgatar valores do form  **************************/
if (isset($_GET['codCondNao']))	$codCondNao	= DHCUtil::antiInjection($_GET["codCondNao"]);
if (isset($_GET['codCondSim']))	$codCondSim	= DHCUtil::antiInjection($_GET["codCondSim"]);
if (isset($_GET['acao']))			$acao		= DHCUtil::antiInjection($_GET["acao"]);

if (!isset($codUsuario)) 	DHCErro::halt('Falta de parâmetros 2');
if (!isset($codCondNao)) 	DHCErro::halt('Falta de parâmetros 3');
if (!isset($codCondSim)) 	DHCErro::halt('Falta de parâmetros 4');

if ($acao != 'del' && $acao != 'add') DHCErro::halt('Parâmetros incorretos');

/************************** Salvar formulário de cadastro **************************/
/** Validação das máscaras **/
$valido		= DHCUtil::validaMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));
if ($valido !== true) {
	echo "O campo '".$valido."' está inválido !!!";
	exit;
}

/** Validação **/
if (($acao == 'add') && ((!$codCondNao) || ($codCondNao == null) || ($codCondNao == 'null'))) {
	echo "Por favor selecione um Condomínio válido !!!";
	exit;
}

if (($acao == 'del') && ((!$codCondSim) || ($codCondSim == null) || ($codCondSim == 'null'))) {
	echo "Por favor selecione um Condomínio válido !!!";
	exit;
}

/** Retirar as máscaras **/
DHCUtil::retiraMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));


if ($acao == 'add') {
	$err = MCUsuarios::associaCondominio($codUsuario, $codCondNao);
	if ($err) {
		echo $err;
		exit;
	}
}elseif ($acao == 'del') {
	$conds	= explode(',',$codCondSim);
	for ($i = 0; $i < sizeof($conds); $i++) {
		$err = MCUsuarios::desassociaCondominio($codUsuario, $conds[$i]);
		if ($err) {
			echo $err;
			exit;
		}
	}
}
?>