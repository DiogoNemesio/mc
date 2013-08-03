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
## Resgatando valores postados
#################################################################################
if (isset($_GET['id'])) {
	$id = \Zage\Util::antiInjection($_GET["id"]);
}elseif (isset($_POST['id'])) {
	$id = \Zage\Util::antiInjection($_POST["id"]);
}else{
	$id = null;
}

#################################################################################
## Descompactar as variáveis
#################################################################################
\Zage\Util::descompactaId($id);

if (!isset($codCondominio)) $codCondominio = null;


#################################################################################
## Cria o objeto do Menu
#################################################################################
//$menu	= new DHCDHXMenu("DMenu");
$menu	= new \Zage\Menu(\Zage\Menu::TIPO1);
$menu->setTarget("IFCentral");

#################################################################################
## Carrega os menus do banco
#################################################################################
$menus	= \Menu::DBGetMenuItens($system->getUsuario());

#################################################################################
## Adiciona os menus no objeto
#################################################################################
foreach ($menus as $dados) {
	if ($dados->codTipo == "M") {
		$menu->adicionaPasta($dados->codMenu, $dados->menu, $dados->icone,$dados->codMenuPai);
	}elseif ($dados->codTipo == "L") {
		$menu->adicionaLink($dados->codMenu, $dados->menu, $dados->icone, $dados->link, $dados->descricao,$dados->codMenuPai);
	}else{
		die('Tipo de Menu desconhecido');
	}
}

#################################################################################
## Carregando o template html
#################################################################################
$tpl	= new \Zage\Template();
$tpl->load(\Zage\Util::getCaminhoCorrespondente(__FILE__, \Zage\ZWS::EXT_HTML));

#################################################################################
## Define os valores das variáveis
#################################################################################
$tpl->set('MENU_CODE'	,$menu->getHtml());
$tpl->set('URL_FORM'	,$_SERVER['REQUEST_URI']);

#################################################################################
## Por fim exibir a página HTML
#################################################################################
$tpl->show();

?>