<?php
if (defined('SITE_ROOT')) {
	include_once(SITE_ROOT . 'include.php');
}else{
	include_once('../include.php');
}

/** Lista com o Planos do Mega Condomínio **/
$aPlanos	= $system->DBGetPlanos();
$tPlanos	= '';

/*for ($i = 0; $i < sizeof($aPlanos); $i++) {
	if (($i%2) == 0) {
		$classe	= 'MCTablePar';
	}else{
		$classe	= 'MCTableImpar';
	}
	
	$tPlanos	.= ' 
		<tr align="center" class="'.$classe.'">
		<td class="wapTexto" width="27%">'.$aPlanos[$i]->nome.'</td>
		<td class="wapTexto" width="26%">'.utf8_encode($aPlanos[$i]->descricao).'</td>
		<td class="wapTexto" width="8%">'.$aPlanos[$i]->valorApresentado.'</td>
		</tr>
	';
}
*/


$grid	= new DHCGrid('GPlanos');
$grid->setAutoWidth(true);
$grid->setAutoHeight(true);
$grid->setSkin($system->config->skin);
$grid->adicionaColuna('PLANO'			,120	,'left'	,'ro'	,'nome');
$grid->adicionaColuna('Nº DE UNIDADES'	,200	,'left'	,'ro'	,'descricao');
$grid->adicionaColuna('VALOR'			,300	,'left'	,'ro'	,'valorApresentado');
$grid->loadObjectArray($aPlanos);




/** Carregando o template html **/
$template	= new DHCHtmlTemplate();
$template->loadTemplate(SITE_HTML_PATH . 'planos.html');

/** Define os valores das variáveis **/
$template->assign('PLANOS'		,$tPlanos);
$template->assign('GRID'		,$grid->getHtmlCode());

/** Por fim exibir a página HTML **/
echo $template->getHtmlCode();

?>