<?php

/**
 * Rotinas gerais do Sistema
 * 
 * @package: DHCDHXMenu
 * @created: 02/04/2010
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class DHCDHXMenu {

	/**
	 * Variável que vai armazenar o codigo javascript
	 */
	protected static $js;

	/**
	 * Variável que vai armazenar o codigo xml
	 */
	protected static $xml;
	
	/**
	 * Variável que vai armazenar o id do div (deve está declarado no html)
	 */
	protected static $divId;
	
	/**
	 * Array que vai armazenar os menus 
	 */
	protected static $menus;
		
	/**
     * Construtor
     *
	 * @return void
	 */
	public function __construct($divId) {
		
		global $system;
		
		/** Salvar o divid **/
		$this->setDivId($divId);
		
		/** Inicializar os arrays **/
		$this->menus	= array();
		
		/** Inicializar o código XML **/
		$this->xml		= '<menu>';
		//$this->xml		.= "<item id=\"Logo\" img=\"".ICON_IMG."\" imgdis=\"".ICON_IMG."\" title=\"Logo\"/>";
		//$this->xml		.= '<item id="sepLogo" type="separator"/>';
		
		/** Inicializar o código JS **/
		$this->js		= "
<script>
	var menu;
	menu	= new dhtmlXMenuObject(\"".$this->getDivId()."\",'".$system->getSkinName()."');
	menu.setIconsPath('%IMG_URL%');
	menu.loadXMLString('%XML_STRING%');
";

	}
	
    /**
     * Resgatar o divId
     *
     * @return string
     */
    protected function getDivId () {
    	return $this->divId;
    }
    
    /**
     * Definir o divID
     *
     * @param string $valor
     */
    protected function setDivId ($valor) {
    	$this->divId	= $valor;
    }

    /**
     * Resgatar o XML
     *
     * @return string
     */
    public function getXML () {
    	return $this->xml;
    }
    	
    /**
     * Resgatar o JS
     *
     * @return string
     */
    public function getJS () {
    	return $this->js;
    }

    /**
     * Adicionar um menu
     *
     * @param string $codigo
     * @param string $menu
     * @param string $descricao
     * @param string $tipo
     * @param string $link
     * @param string $nivel
     * @param string $menuPai
     * @param string $icone
     */
    public function addMenu($codigo,$menu,$descricao,$tipo,$link,$nivel,$menuPai,$icone,$codCondominio = null) {
    	global $system;
    	
    	$info		= MCUsuarios::getInfo($system->getCodUsuario());
    	$codTipo	= $info->codTipo;
    	
    	$url = DHCDHXMenu::montaUrl($link, $codigo, $codTipo);

    	/** Verifica se o nível da arvore **/
    	if ($nivel == 0) {
    		if ($tipo == 'M') {
    			$this->addItem($codigo,$menuPai,$menu,$icone,$nivel);
    		}elseif ($tipo == 'L') {
    			$this->addLink($codigo,$menu,$url,$icone,$descricao,$menuPai);
        	}else{
				$system->halt('Tipo de menu desconhecido ('.$tipo.')',false,false,true);
			}
    	}elseif ($nivel > 0) {
    		$this->addSubMenu($this->menus,$menuPai,$codigo,$menu,$descricao,$tipo,$url,$nivel,$icone);
    	}
    }
    
    /**
     * Montar a URL de um Menu 
     * 
     * @param string $link
     * @param number $codMenu
     * @param string $codTipo
     */
    public static function montaUrl($link,$codMenu,$codTipo) {
    	global $system;
    	
    	if (($codTipo == 'S') || ($codTipo == 'SS') || ($codTipo == 'P') || ($codTipo == 'F')) {
    		/** Resgatar o condomínio **/
    		$codCondominio 	= MCUsuarios::getCondominio($system->getCodUsuario());
    	}else{
    		$codCondominio	= null;
    	}
    	
    	if ($codCondominio != null) {
    		if (MCUsuarios::temAcessoAoCondominio($system->getCodUsuario(), $codCondominio) == false) {
    			$system->halt('SECURITY: Tentativa indevida de acesso ao menu do usuário: '.$system->getUsuario().' IP: '.$_SERVER['REMOTE_ADDR'].' !!!',false,false,false);
    		}
    	}
    	
    	/** verifica se a url já tem alguma variável **/
    	if (strpos($link,'?') !== false) {
    		$vars	= '&'.substr(strstr($link, '?'),1);
    		$link	= substr($link,0,strpos($link, '?'));
    	}else{
    		$vars	= '';
    	}
    	
    	$id		= DHCUtil::encodeUrl('codCondominio='.$codCondominio.$vars);

    	if (isset($id)) {
    		$url		= $link."?_codMenu_=".$codMenu.'&id='.$id;
    	}else{
    		$url		= $link."?_codMenu_=".$codMenu;
    	}
    	
    	return ($url);
    }
    
    /**
     * Adicionar um Item
     */
    protected function addItem ($codigo,$itemPai,$nome,$icone,$nivel) {
    	global $system;
    	if (array_key_exists($codigo,$this->menus)) {
    		$system->halt('Menu duplicado: '.$codigo,false,false,true);
    	}
    	$this->menus[$codigo]			= array();
    	$this->menus[$codigo]["TIPO"]	= 'M';
    	$this->menus[$codigo]["OBJ"]	= new DHCDHXMenuItem($codigo,$itemPai);
    	$this->menus[$codigo]["OBJ"]->setNome($nome);
    	$this->menus[$codigo]["OBJ"]->setIcone($icone);
    	$this->menus[$codigo]["OBJ"]->setNivel($nivel);
	}

        
    /**
     * Adicionar um Link
     */
	protected function addLink ($codigo,$nome,$url,$icone,$descricao,$itemPai) {
		global $system;
    	if (array_key_exists($codigo,$this->menus)) {
    		$system->halt('Menu duplicado: '.$codigo,false,false,true);
    	}
    	
    	$this->menus[$codigo]			= array();
    	$this->menus[$codigo]["TIPO"]	= 'L';
    	$this->menus[$codigo]["OBJ"]	= new DHCDHXMenuLink($codigo,$itemPai);
    	$this->menus[$codigo]["OBJ"]->setNome($nome);
    	$this->menus[$codigo]["OBJ"]->setURL($url);
    	$this->menus[$codigo]["OBJ"]->setIcone($icone);
    	$this->menus[$codigo]["OBJ"]->setDescricao($descricao);
    }
    
    /**
     * Gerar os codigos fontes JS e XML
     */
    public function render () {
    	global $system;
    	    	
    	/** Gerar o código dos itens **/
    	$this->geraXmlMenu($this->menus);
    	$this->js	.= "</script>";
    	$this->xml	.= "</menu>";
    	//$system->log->debug->debug("XML: ".$this->xml);
    	
    }

    /**
     * Enter description here...
     *
     * @param array $array
     * @param integer $menuPai
     * @param integer $codigo
     * @param string $menu
     * @param string $descricao
     * @param string $tipo
     * @param string $link
     * @param integer $nivel
     * @param string $icone
     */
    protected function addSubMenu(&$array,$menuPai,$codigo,$menu,$descricao,$tipo,$link,$nivel,$icone) {
    	foreach ($array as $key => $value) {
    		if ((is_object($array[$key]["OBJ"])) && ($array[$key]["OBJ"]->getCodigo() == $menuPai)) {
    			$array[$key]["OBJ"]->addMenu($codigo,$menu,$descricao,$tipo,$link,$nivel,$menuPai,$icone);
    		}elseif ((is_object($array[$key]["OBJ"])) && ($array[$key]["TIPO"] == 'M')) {
    			$this->addSubMenu($array[$key]["OBJ"]->menus,$menuPai,$codigo,$menu,$descricao,$tipo,$link,$nivel,$icone);
    		}
    	}
    }
    
    /**
     * Gera o xml dos menus
     *
     * @param array $array
     */
    protected function geraXmlMenu ($array) {
    	foreach ($array as $key => $value) {
			if ($array[$key]["OBJ"]->getIcone()) {
				$menuIcone	= ' img="'.$array[$key]["OBJ"]->getIcone().'" imgdis="'.$array[$key]["OBJ"]->getIcone().'"';
			}else{
				$menuIcone	= '';
			}

    		if ((is_object($array[$key]["OBJ"])) && ($array[$key]["TIPO"] == 'M')) {
				if ($array[$key]["OBJ"]->getIcone()) {
   					$menuIcone	= ' img="'.$array[$key]["OBJ"]->getIcone().'" imgdis="'.$array[$key]["OBJ"]->getIcone().'"';
   				}else{
   					$menuIcone	= '';
    			}

    			$this->xml .= '<item id="MENU'.$array[$key]["OBJ"]->getCodigo().'" text="'.$array[$key]["OBJ"]->getNome().'"'.$menuIcone.'>';
    			$this->geraXmlMenu($array[$key]["OBJ"]->menus);
    			$this->xml .= '</item>';
    		}elseif ((is_object($array[$key]["OBJ"])) && ($array[$key]["TIPO"] == 'L')) {
	   			if ($array[$key]["OBJ"]->getURL()) {
   					$link 	= '<href target="IFCentral"><![CDATA['.$array[$key]["OBJ"]->getURL().']]></href>';
   				}else{
	   				$link	= '';
   				}

				$this->xml	.= '<item id="BOTAO'.$array[$key]["OBJ"]->getCodigo().'" title="'.$array[$key]["OBJ"]->getDescricao().'" text="'.$array[$key]["OBJ"]->getNome().'"'.$menuIcone.'>';
				$this->xml	.= $link;
				$this->xml	.= '</item>';
    		}
    	}
    }
}


