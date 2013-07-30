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

if (isset($_GET['err'])){
	$err	= DHCUtil::antiInjection($_GET["err"]);
}else{
	$err 	= '';
}

/*************Restagar o condominio*************/
if (isset($_GET['codCondominio'])){
	$codCondominio		= DHCUtil::antiInjection($_GET["codCondominio"]);
}else{
	echo "<script>alert('Erro variável codCondominio perdida !!!!');</script>";
	exit;
}
/************* Restagar o código do usuário *************/
if (isset($_GET['codUsuario'])){
	$codUsuario		= DHCUtil::antiInjection($_GET["codUsuario"]);
}else{
	$codUsuario		= null;
}
/************* Restagar o código do usuário ************
if ($codCondominio == null) {
	echo "<script> alert('Não existe condomínio cadastrado');</script>";
	exit();
}else{
	$email			= null;
	$usuario		= null;
	$nome 			= null;
	$tipo_usuario	= null;
	$senha1			= null;
	$senha2 		= null;
		
}*/
/************************** Salvar **************************/
if ((isset($_POST['salvar']))) {

	if (isset($_POST['condominio']))	$codCondominio	= DHCUtil::antiInjection($_POST["condominio"]);
	if (isset($_POST['usuario']))		$usuario		= DHCUtil::antiInjection($_POST["usuario"]);
	if (isset($_POST['nome']))			$nome			= DHCUtil::antiInjection($_POST["nome"]);
	if (isset($_POST['email']))			$email			= DHCUtil::antiInjection($_POST["email"]);
	if (isset($_POST['tipoUsuario']))	$codTipo		= DHCUtil::antiInjection($_POST["tipoUsuario"]);
	if (isset($_POST['senha1']))		$senha1			= DHCUtil::antiInjection($_POST["senha1"]);
	if (isset($_POST['senha2']))		$senha2			= DHCUtil::antiInjection($_POST["senha2"]);
	
	if ($codUsuario != null) {
		$condominioUsuario = $codCondominio;
	}else{
		$condominioUsuario = null;
	}
	
	/** Validação dos Campos **/
	$err	= null;
	
	if ($codCondominio == null) {
		$err	= "Erro:O Campo \"CONDOMINIO\" é obrigatório !!!";
	}
	
	if ($usuario == null) {
		$err	= "Erro:O Campo \"USUÁRIO\" é obrigatório !!!";
	}
	
	if ($nome == null) {
		$err	= "Erro:O Campo \"NOME\" é obrigatório !!!";
	}
	
	if ($email == null) {
		$err	= "Erro:O Campo \"EMAIL\" é obrigatório !!!";
	}
	
	if ($codTipo == null) {
		$err	= "Erro:O Campo \"TIPO USUÁRIO\" é obrigatório !!!";
	}
	
	if ($codUsuario == null){
		$errSenha = null;
		if ($senha1 == null) {
			$err	= "Erro:O Campo \"SENHA\" é obrigatório !!!";
			$errSenha = 'Erro';
		}
		if ($senha2 == null) {
			$err	= "Erro:O Campo \"SENHA 2\" é obrigatório !!!";
			$errSenha = 'Erro';
		}
		if ($senha1 != $senha2) {
			$err	= "Erro:As \SENHAS\ não conferem !!!";
			$errSenha = 'Erro';
		}
		if ($errSenha == null){
			$senha = $senha1;
		}
		
	}else{
		if (($senha1==null) && ($senha2 == null)) {
			$senha = null;
		}else{
			if ($senha1 != $senha2){
				$err	= "Erro:As \SENHAS\ não conferem !!!";
			}else{
				$senha = $senha1;
			}
		}
	}

	if ($err == null) {
		if ($senha != null){
			$senhaCrip	= md5('MC'.$usuario.'|'.$senha); //Formato da senha
		}else{
			$senhaCrip = null;
		}
		$err = MCUsuarios::salva($codUsuario,$usuario,$nome, $senhaCrip, $email, $codTipo, $codCondominio);
		if (is_numeric($err)) {
			$err			= null;
		}
	}	
}else{
	if ($codUsuario == null) {
		
		$usuario 			= null;
		$nome 				= null;
		$email 				= null;
		$codTipo			= null;
		$senha1				= null;
		$senha2				= null;
		$condominioUsuario	= $codCondominio;
		 
	}else{
		
		$info 			= MCUsuarios::getInfo($codUsuario);

		$usuario			= $info->usuario;
		$nome 				= $info->nome;
		$email	 			= $info->email;
		$codTipo			= $info->codTipo;
		$condominioUsuario 	= $info->codCondominio;
	}
}

/************************** Restagatar valores do banco **************************/

/** Resgatar os tipos de usuarios **/
$aTipoUsuarios = MCUsuarios::listaTipoUsuario();
$oTipoUsuarios = '';
$oTipoUsuarios .= "<option value=''><----SELECIONE----></option>";
$selected = null;
for($i=0; $i<sizeof($aTipoUsuarios);$i++) {
	if ($codTipo != null) {
		if ($codTipo == $aTipoUsuarios[$i]->codTipo) {
			$selected .= 'selected';
		}else{
			$selected = null;
		}
	}

	$oTipoUsuarios .= "<option $selected value='".$aTipoUsuarios[$i]->codTipo."'>".$aTipoUsuarios[$i]->codTipo. ' - '.$aTipoUsuarios[$i]->descricao.'</option>';
}

/** Resgatar os condominios (caso seja carregado o codCondominio não haverá opções de outro condominio) **/
$aCondominios = MCCondominio::lista();
$oCondominios = '';
$selected = '';
$disabled = '';
$oCondominios .= "<option value=''><----SELECIONE----></option>";
for($i=0; $i<sizeof($aCondominios);$i++) {
	if ($condominioUsuario != null){
		if ($condominioUsuario == $aCondominios[$i]->codCondominio){
			$oCondominios = '';
			$selected .= "selected";
			$disabled .= "disabled";
		}else{
			$selected = '';
			$RO = '';
		}
	}
	
	$oCondominios .= "<option $selected $disabled value='".$aCondominios[$i]->codCondominio."'>".$aCondominios[$i]->codCondominio. ' - '.$aCondominios[$i]->nomeCondominio.'</option>';
}

/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(MegaCondominio::getCaminhoCorrespondente(__FILE__, 'html'));


/** Define os valores das variáveis **/
$template->assign('URL_FORM'		,$_SERVER['REQUEST_URI']);
$template->assign('EMAIL'			,$email);
$template->assign('USUARIO'			,$usuario);
$template->assign('TIPO_USUARIO'	,$oTipoUsuarios);
$template->assign('CONDOMINIOS'		,$oCondominios);
$template->assign('NOME'			,$nome);
$template->assign('CONDOMINIOS_URL'	,CONDOMINIOS_URL);
$template->assign('RO'				,$RO);
$template->assign('USUARIO'			,$usuario);
$template->assign('NOME'			,$nome);
$template->assign('EMAIL'			,$email);


/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>