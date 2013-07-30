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
if (isset($_POST['codEspaco']))			$codEspaco		= DHCUtil::antiInjection($_POST["codEspaco"]);
if (isset($_POST['codCondominio']))		$codCondominio	= DHCUtil::antiInjection($_POST["codCondominio"]);
if (isset($_POST['nome']))				$nome			= DHCUtil::antiInjection($_POST["nome"]);
if (isset($_POST['descricao']))			$descricao		= DHCUtil::antiInjection($_POST["descricao"]);
if (isset($_POST['tempoMaximo']))		$tempoMaximo	= DHCUtil::antiInjection($_POST["tempoMaximo"]);
if (isset($_POST['indConfirmacao']))	$indConfirmacao	= DHCUtil::antiInjection($_POST["indConfirmacao"]);
if (isset($_POST['valor']))				$valor			= DHCUtil::antiInjection($_POST["valor"]);


if (!isset($codCondominio))		{
	echo "falta de parâmetros";
	exit;
}


/** Validação das máscaras **/
$valido		= $system->mask->validaMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

if ($valido !== true) {
	echo "Erro:O campo '".$valido."' está inválido !!!";
	exit;
}

$err	= null;


$system->mask->retiraMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));

if ($err == null) {
	$err = MCEspaco::salva($codEspaco,$codCondominio,$nome,$descricao,$tempoMaximo,$indConfirmacao,$valor);
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