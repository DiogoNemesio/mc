<?php
if (defined ( 'DOC_ROOT' )) {
	include_once (DOC_ROOT . 'include.php');
} else {
	include_once ('../include.php');
}


$tpl		= new \Zage\Template(\Zage\Util::getCaminhoCorrespondente(__FILE__, \Zage\ZWS::EXT_HTML));
$menu	= new \Zage\Menu(\Zage\Menu::TIPO1);

$menu->adicionaPasta("CAD", "Cadastros", null);
$menu->adicionaPasta("NOT", "Notas", null);
$menu->adicionaPasta("REL", "Relatórios", null);
$menu->adicionaLink("USU", "Usuários", null, "#", "Cad Usuários","CAD");
$menu->adicionaSeparador("SEP1","CAD");
$menu->adicionaLink("UNU", "Unimed", null, "#", "Cad Unimed","CAD");
$menu->adicionaPasta("TIP", "Tipos", null,"CAD");
$menu->adicionaLink("ACO", "Acomodação", null, "#", "Cad Acomodacao","TIP");
$menu->adicionaLink("ATE", "ATendimento", null, "#", "Cad Atendimento","TIP");
$menu->adicionaLink("RELNO", "Relação", null, "#", "Rel Notas","NOT");
$menu->adicionaPasta("GER", "Gerencial", null,"REL");
$menu->adicionaLink("CONTLIQ", "Contribuição Liq", null, "#", "Contribuição Liquida","GER");
$menu->adicionaPasta("OPE", "Operacional", null,"REL");
$menu->adicionaLink("RESFIN", "Resumo Financeiro", null, "#", "Resumo Financeiro","OPE");
$menu->adicionaPasta("IMP", "Importações", null);
$menu->adicionaLink("IMPPTU", "PTU", null, "#", "Imp PTU","IMP");
$menu->adicionaPasta("OP", "Opções", null);
$menu->adicionaLink("PAR", "Parâmetros", null, "#", "Parâmetros","OP");
$menu->adicionaPasta("AJ", "Ajuda", null);
$menu->adicionaLink("SOB", "Sobre", null, "#", "Sobre","AJ");
$menu->adicionaLink("INI", "Início", null, "#", "Ir ao início");
$menu->adicionaLink("SA", "Sair", null, "#", "Sair");

$hMenu = $menu->getHtml();

$tpl->MENU	= $hMenu; 
$tpl->show();

?>
