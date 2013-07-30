<?php
if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}


//$system->log->debug->debug("_GET: ".serialize($_GET));
//$system->log->debug->debug("_POST: ".serialize($_POST));

/** Resgatando valores postados **/
if (isset($_POST['id'])) {
	$id = DHCUtil::antiInjection($_POST["id"]);
}else{
	echo "Requisição inválida !!";
	exit;
}

/** Descompactar as variáveis **/
if ($id) {
	$var    = base64_decode($id);
	$vars   = explode("&",$var);
	for ($i = 0; $i < sizeof($vars); $i++) {
		list($variavel,$valor)  = explode('=',$vars[$i]);
		eval('$'.$variavel.' = "'.$valor.'";');
	}
}

/************************** Resgatar valores do form  **************************/
if (isset($_POST['nomeCondominio']))	$nomeCondominio		= DHCUtil::antiInjection($_POST["nomeCondominio"]);
if (isset($_POST['idCondominio']))		$idCondominio		= DHCUtil::antiInjection($_POST["idCondominio"]);
if (isset($_POST['endereco']))			$endereco			= DHCUtil::antiInjection($_POST["endereco"]);
if (isset($_POST['bairro']))			$bairro				= DHCUtil::antiInjection($_POST["bairro"]);
if (isset($_POST['numero']))			$numero				= DHCUtil::antiInjection($_POST["numero"]);
if (isset($_POST['cep']))				$cep				= DHCUtil::antiInjection($_POST["cep"]);
if (isset($_POST['codEstado']))			$codEstado			= DHCUtil::antiInjection($_POST["codEstado"]);
if (isset($_POST['codCidade']))			$codCidade			= DHCUtil::antiInjection($_POST["codCidade"]);
if (isset($_POST['numUnidades']))		$numUnidades		= DHCUtil::antiInjection($_POST["numUnidades"]);

if (!isset($codCondominio))		{
	$codCondominio		= null;
}

/************************** Salvar formulário de cadastro **************************/
/** Validação dos Campos **/
$err	= null;
	
if ($nomeCondominio == null) {
	$err	= "Erro:O Campo NOME é obrigatório !!!";
}

if ($idCondominio == null) {
	$err	= "Erro:O Campo IDENTIFICAÇÃO é obrigatório !!!";
}

/** Verificar se a identificação é válida **/
switch (substr($idCondominio,0,1)) {
	case '_':
	case '.':
	case ';':
	case ' ':
	case '#':
	case '@':
	case '!':
	case '$':
	case '%':
	case '"':
	case '\'':
	case '&':
	case '*':
	case '(':
	case ')':
	case '+':
	case '-':
	case '{':
	case '[':
	case '}':
	case ']':
	case '/':
	case '\\':
	case '?':
	case '`':
	case '\'':
	case '':
	case '\'':
	case '<':
	case '>':
		$err	= "Erro:Identificação inválida !!!";
}
	
switch (strtoupper($idCondominio)) {
	case '__PHPMYADMIN':
	case '_ADMIN_':
	case 'BIN':
	case 'CLASSES':
	case 'CLASSE':
	case 'CSS':
	case 'ETC':
	case 'HTML':
	case 'IMGS':
	case 'IMG':
	case 'IMAGES':
	case 'IMAGE':
	case 'JS':
	case 'LOG':
	case 'ADM':
	case 'MC':
	case 'PACKAGES':
	case 'XML':
	case 'DP':
		$err	= "Erro:Identificação inválida !!!";
}
	
if ($endereco == null) {
	$err	= "Erro:O Campo ENDEREÇO é obrigatório !!!";
}

if ($bairro == null) {
	$err	= "Erro:O Campo BAIRRO é obrigatório !!!";
}

if ($numero == null) {
	$err	= "Erro:O Campo NUMERO é obrigatório !!!";
}

if ($cep == null) {
	$err	= "Erro:O Campo CEP é obrigatório !!!";
}

if ($codEstado == null) {
	$err	= "Erro:O Campo ESTADO é obrigatório !!!";
}

if ($codCidade == null) {
	$err	= "Erro:O Campo CIDADE é obrigatório !!!";
}

if ($numUnidades == null) {
	$err	= "Erro:O Campo NÚMERO DE UNIDADES é obrigatório !!!";
}

if ($numUnidades < 1) {
	$err	= "Erro:O Campo NÚMERO DE UNIDADES deve ser maior que zero !!!";
}

if ($err == null) {
	$oldCod	= $codCondominio;
	$err = MCCondominio::salva($codCondominio,$nomeCondominio, $idCondominio, $endereco, $bairro, $numero, $codCidade, $cep, $numUnidades);
	if ($err) {
		echo $err;
		exit;
	}else{
		if ($oldCod <> $codCondominio) {
			echo $codCondominio;
			exit;
		} 
	}
}else{
	echo $err;
	exit;
}

?>