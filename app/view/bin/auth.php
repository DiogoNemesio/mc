<?php

global $log,$system,$db;

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}


/** Verificar se o usuário e senha estão sendo passados através do form **/
if ((isset($_POST['usuario'])) && (isset($_POST['senha']))) {
	$usuario	= \Zage\Util::antiInjection($_POST['usuario']);
	$senha		= \Zage\Util::antiInjection($_POST['senha']);
	$senhaCrip	= \Usuarios::crypt($usuario, $senha);
}else{
	$usuario	= '';
	$senha		= '';
}

/** Limpando a variável da mensagem **/
$mensagem		= '';

/** Verifica se o usuário já está conectado **/
if ((!$system->estaAutenticado())) {

	if (($usuario) && ($senha)) {

		$valUsuario	= new \Zage\Auth\validaUsuario();
		$valSenha	= new \Zage\Auth\validaSenha();

		if (!$valUsuario->isValid($usuario)) {
    		$r			= \Zend\Authentication\Result::FAILURE_CREDENTIAL_INVALID;
    		$result		= new \Zend\Authentication\Result($r,$usuario,$valUsuario->getMessages());
		}elseif (!$valSenha->isValid($senha)) {
    		$r			= \Zend\Authentication\Result::FAILURE_CREDENTIAL_INVALID;
    		$result		= new \Zend\Authentication\Result($r,$usuario,$valSenha->getMessages());
		}else{
			$authAdap	= new \Zage\Auth($usuario,$senhaCrip);
			$result		= $authAdap->authenticate();
		}
		
		$log->debug('Result: '.serialize($result));
		
		if (!$result->isValid()) {

			$m			= $result->getMessages();
			$mensagem	= $m[0];
			
		    $log->debug('Retorno: '.$mensagem);
			
			include_once(BIN_PATH . '/login.php');
			exit;
		} else {
			$system->usuario->setUsuario($usuario);
			$log->debug('Usuário autenticado com sucesso !!! ');
			$system->setAutenticado();
		}
	}else{
		include_once(BIN_PATH . '/login.php');
		exit;
	}
}

?>