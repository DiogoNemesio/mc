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
if (isset($_POST['usuario']))	$usuario	= DHCUtil::antiInjection($_POST["usuario"]);
if (isset($_POST['nome']))		$nome		= DHCUtil::antiInjection($_POST["nome"]);
if (isset($_POST['email']))		$email		= DHCUtil::antiInjection($_POST["email"]);
if (isset($_POST['codTipo']))	$codTipo	= DHCUtil::antiInjection($_POST["codTipo"]);
if (isset($_POST['senha']))		$senha		= DHCUtil::antiInjection($_POST["senha"]);
if (isset($_POST['codStatus']))	$codStatus	= DHCUtil::antiInjection($_POST["codStatus"]);

if (!isset($codUsuario))		{
	$codUsuario		= null;
}

/************************** Salvar formulário de cadastro **************************/
/** Validação das máscaras **/
$valido		= DHCUtil::validaMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));
if ($valido !== true) {
	echo "O campo '".$valido."' está inválido !!!";
	exit;
}

/** Validação dos Campos **/
$err	= null;

DHCUtil::retiraMascarasForm(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'xml'));


if ($err == null) {
	$oldCod	= $codUsuario;
	$err = MCUsuarios::salva($codUsuario, $usuario, $nome, $senha, $email, $codTipo, $codStatus, null);
	if ($err) {
		echo $err;
		exit;
	}else{
		/** Verifica se é para associar a algum condomínio **/
		if ($system->ehAdmin($system->getUsuario()) == false) {
			if (!MCUsuarios::temAcessoAoCondominio($codUsuario, $codCondominio)) {
	    		$codCondominio 	= MCUsuarios::getCondominio($system->getCodUsuario());
				$err = MCUsuarios::associaCondominio($codUsuario, $codCondominio);
				if ($err) {
					echo $err;
					exit;
				}
			}
		}
		
		if ($oldCod <> $codUsuario) {
			echo $codUsuario;
			exit;
		} 
	}
}else{
	echo $err;
	exit;
}

?>