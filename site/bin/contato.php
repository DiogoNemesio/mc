<?php
if (defined('SITE_ROOT')) {
	include_once(SITE_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

/** Resgatar os Estados **/
$aEstados = $system->DBGetEstados();
$tEstados = '';
$tEstados .= '<option value="">--</option>';
for($i=0; $i<sizeof($aEstados);$i++) {
	$tEstados .= '<option value="'.$aEstados[$i]->codEstado.'">'.$aEstados[$i]->codEstado.'</option>';
}

/********** Salvar Contato *********/
$err = null;

if (isset($_POST['enviar'])) {
	
echo '1';
	if (isset($_POST['nome'])){
		$nome			= DHCUtil::antiInjection($_POST['nome']);
	}else{
		$err = '*Nome é obrigatório';
	}
	if (isset($_POST['email'])){
		$email			= DHCUtil::antiInjection($_POST['email']);
	}else{
		$err = '*E-mail é obrigatório';
	}
	if (isset($_POST['ddd'])){
		$ddd			= DHCUtil::antiInjection($_POST['email']);
	}
	if (isset($_POST['telefone'])){
		$fone			= DHCUtil::antiInjection($_POST['email']);
	}
	if (isset($_POST['estado'])){
		$estado			= DHCUtil::antiInjection($_POST['email']);
	}else{
		$err = '*Estado é obrigatório';
	}
	if (isset($_POST['cidade'])){
		$cidade			= DHCUtil::antiInjection($_POST['email']);
	}else{
		$err = '*Cidade é obrigatório';
	}
	if (isset($_POST['comentario'])){
		$comentarios	= DHCUtil::antiInjection($_POST['email']);
	}else{
		$err = '*Comentário é obrigatório';
	}
	if (isset($_POST['receberEmail'])){
		$receberEmail	= DHCUtil::antiInjection($_POST['email']);
	}
	
	if (!$err) {
		$system->DBSalvaComentario($nome, $email, $ddd, $fone, $codEstado, $codCidade, $comentarios, $receberEmail);
	}
}
/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(SITE_HTML_PATH . 'contato.html');

/** Define os valores das variáveis **/
$template->assign('ESTADOS'		,$tEstados);
$template->assign('MENSAGEM'	,$err);
$template->assign('URL_FORM'	,$_SERVER['REQUEST_URI']);

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>