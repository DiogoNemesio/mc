<?php

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
## Resgata os parâmetros passados pelo menu
#################################################################################
if (isset($_GET["id"])) 	{
	$id = \Zage\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST["id"])) 	{
	$id = \Zage\Util::antiInjection($_POST["id"]);
}elseif (isset($id)) 	{
	$id = \Zage\Util::antiInjection($id);
}else{
	die('Parâmetro inválido 1');
}

#################################################################################
## Descompacta o ID
#################################################################################
\Zage\Util::descompactaId($id);


$condominios		= \Condominio::lista();

$grid 				= new \Zage\Grid('GCondominio');
$grid->adicionaTexto('IDENTIFICADOR'	,25		,$grid::CENTER	,'condominio');
$grid->adicionaTexto('NOME'				,40		,$grid::CENTER	,'nomeCondominio');
$grid->adicionaTexto('UNIDADES'			,20		,$grid::CENTER	,'qtdeUnidades');
$grid->adicionaBotao(\Zage\Grid\Coluna\Botao::MOD_EDIT);
$grid->adicionaBotao(\Zage\Grid\Coluna\Botao::MOD_REMOVE);
$grid->importaDadosArray($condominios);

$i=0;
foreach ($condominios as $dados) {
	$id     = base64_encode("codCondominio=".$dados->codCondominio."&_codMenu_=".$_codMenu_);
	$grid->setValorCelula($i,3,BIN_URL.'/editCondominio.php?id='.$id.'^_self');
	$grid->setValorCelula($i,4,BIN_URL.'/admExcCondominio.php?id='.$id.'^_self');
	$i++;
}

#################################################################################
## Gera a localização (breadcrumb)
#################################################################################
$local          = $system->geraLocalizacao($_codMenu_, $system->usuario->getTipo());

#################################################################################
## Gerar a url de adicão
#################################################################################
$urlAdd                 = BIN_URL.'editCondominio?id='.base64_encode("_codMenu_=".$_codMenu_);

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\Template();
$tpl->load(HTML_PATH . "gridTemplate.html");

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('MENSAGEM'		,null);
$tpl->set('GRID'			,$grid->getHtmlCode());
$tpl->set('LOCALIZACAO'		,$local);
$tpl->set('NOME'			,"Condomínio");
$tpl->set('URLADD'			,$urlAdd);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();
