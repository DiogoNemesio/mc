<?php

if (defined('DOC_ROOT')) {
	include_once(DOC_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

/** Verificar se o usuário e senha estão sendo passados através do form **/
if ((isset($_POST['usuario'])) && (isset($_POST['senha']))) {
	$usuario	= DHCUtil::antiInjection($_POST['usuario']);
	$senha		= DHCUtil::antiInjection($_POST['senha']);
	$senhaCrip	= MCUsuarios::crypt($usuario, $senha);//md5('MC'.$usuario.'|'.$senha); //Formato da senha
}else{
	$usuario	= '';
	$senha		= '';
}

/** Limpando a variável da mensagem **/
$mensagem		= '';

/** Instanciando o objeto de autenticação **/
$auth			= Zend_Auth::getInstance();

/** Verifica se o usuário já está conectado **/
if ((!$system->estaAutenticado())) {

	if (($usuario) && ($senha)) {

		$valUsuario	= new DHCValUsuario();
		$valSenha	= new DHCValSenha();

		if (!$valUsuario->isValid($usuario)) {
    		$r			= Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
    		$result		= new Zend_Auth_Result($r,$usuario,$valUsuario->getMessages());
		}elseif (!$valSenha->isValid($senha)) {
    		$r			= Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
    		$result		= new Zend_Auth_Result($r,$usuario,$valSenha->getMessages());
		}else{
			$authAdap	= new DHCAuth($usuario,$senhaCrip);
			$result		= $authAdap->authenticate();
		}
		
		$system->log->debug->debug('Result: '.serialize($result));
		
		if (!$result->isValid()) {

			$m			= $result->getMessages();
			$mensagem	= $m[0];
			
		    $system->log->debug->debug('Retorno: '.$mensagem);
			
			include_once(BIN_PATH . '/login.php');
			exit;
		} else {
			$system->usuario->setUsuario($usuario);
			$system->log->debug->debug('Usuário autenticado com sucesso !!! ');
			$system->setAutenticado();
		}
	}else{
		include_once(BIN_PATH . '/login.php');
		exit;
	}
}

?>