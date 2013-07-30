<?php
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}
/** Verifica se o usuário está autenticado **/
include_once(BIN_PATH . 'auth.php');

/** Resgatando valores postados **/
if (isset($_POST['id'])) {
	$id = DHCUtil::antiInjection($_POST["id"]);
}else{
	echo "Requisição inválida !!";
	exit;
}

/** Descompactar as variáveis **/
DHCUtil::descompactaId($id);

/************************** Resgatar valores do form  **************************/
if (isset($_POST['diaPagSal']))		$diaPagSal	= DHCUtil::antiInjection($_POST["diaPagSal"]);
if (isset($_POST['codSkin']))		$codSkin	= DHCUtil::antiInjection($_POST["codSkin"]);
if (isset($_POST['codPlanUni']))	$codPlanUni	= DHCUtil::antiInjection($_POST["codPlanUni"]);

if (!isset($codCondominio))		{
	echo "Erro: falta de parâmetros";
	exit;
}


/** Validação dos Campos **/
$err	= null;

if ((!$codSkin) 	|| ($codSkin 	== '') || ($codSkin 	== 'null')) $codSkin 	= null;
if ((!$codPlanUni)	|| ($codPlanUni == '') || ($codPlanUni 	== 'null')) $codPlanUni 	= null;

if ($err == null) {
	$err = MCCondParametro::salva($codCondominio,$diaPagSal,$codSkin,$codPlanUni);
	if (is_numeric($err)) {
		$err			= null;
	}
	echo $err;
	exit;
}else{
	echo $err;
	exit;
}	

?>