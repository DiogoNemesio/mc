<?php

/**
 * Implementação do MegaCondominio
 * 
 * @package: MegaCondominio
 * @Author: Daniel Henrique Cassela
 * @version: 1.0.1
 * 
 */
class MegaCondominio extends \Zage\ZWS {
	
	/**
	 * Objeto que irá guardar a Instância para implementar SINGLETON (http://www.php.net/manual/pt_BR/language.oop5.patterns.php)
	 */
	private static $instance;
	
	/**
	 * Configurações do Skin que será usado
	 */
	private $skin;
	private $skinBaseDir;
	private $skinName;
	private $formSkinName;
	
	/**
	 * Configurações do CSS
	 */
	private $cssFile;
	
	/**
	 * Configurações do Dynamic Html Load
	 */
	private $dynHtmlLoad;
	
	/**
	 * Instância da classe MCMascara
	 *
	 * @var object
	 */
	public $mask;
	
	
	/**
	 * Construtor
	 *
	 * @return void
	 */
	protected function __construct() {
	/**
	 * Verificar função inicializaSistema() *
	 */
	}
	
	/**
	 * Construtor para implemetar SINGLETON
	 *
	 * @return object
	 */
	public static function getInstance() {
		if (! isset ( self::$instance )) {
			$c = __CLASS__;
			self::$instance = new $c ();
		}
		
		return self::$instance;
	}
	
	/**
	 * Refazer a função para não permitir a clonagem deste objeto.
	 */
	public function __clone() {
		\Zage\Erro::halt ( 'Não é permitido clonar ' );
	}
	
    
	/**
	 * Inicializar o sistema
	 * @return void
	 */
    public function inicializaSistema () {
    	global $log,$db;
    	
    	/** Chama o construtor da classe mae **/
		parent::__construct();	
    	
		$log->debug(__CLASS__.": nova Instância");

		/** Definindo atributos globais a Instância de e-mail (Podem ser alterados no momento do envio do e-mail) **/
		$this->mail->setSubject('.:: Erro no sistema ::.');
		
		/** Definir o css padrao **/
		$this->setCssFile('megaCondominio.css');
		
		/** Definindo o Skin padrão **/
		$this->setSkinBaseDir('default');
		
		/** Definindo o conteúdo do html dinâmico **/
		$this->setDynHtmlLoad(\Parametro::getDinamicHtmlLoad());
		
		/** Carrega as configurações de máscara **/
		$this->mask			= new \Mascara();
		
		/** Inicializa o objeto do usuário para fins de autenticação **/
		$this->usuario		= new \Usuario();
		
    }

    /**
     * Definir o Css
     *
     * @param string $valor
     */
    public function setCssFile($valor) {
    	$this->cssFile	= $valor;
    }
    
    /**
     * Resgatar o CSS
     *
     * @return string
     */
    public function getCssFile() {
    	return ($this->cssFile);
    }
    
    /**
     * Definir o Skin
     *
     * @param string $valor
     */
    public function setSkin($valor) {
		/** Resgatar as configurações do skin **/
		$infoSkin	= $this->DBGetInfoSkin($valor);
		if (!$infoSkin) $this->halt('Configurações do skin não encontradas',false,false,true);
		$this->skin	= $valor;
		$this->setSkinName($infoSkin->skinName);
		$this->setSkinBaseDir($infoSkin->dirBase);
		$this->setFormSkinName($infoSkin->formSkinName);
    }
    
    /**
     * Resgatar o Skin
     *
     * @return string
     */
    public function getSkin() {
    	return ($this->skin);
    }
    
    /**
     * Definir o SkinBaseDir
     *
     * @param string $valor
     */
    public function setSkinBaseDir($valor) {
    	$this->skinBaseDir	= $valor;
    }
    
    /**
     * Resgatar o SkinBaseDir
     *
     * @return string
     */
    public function getSkinBaseDir() {
    	return ($this->skinBaseDir);
    }

    /**
     * Definir o SkinName
     *
     * @param string $valor
     */
    public function setSkinName($valor) {
    	$this->skinName	= $valor;
    }
    
    /**
     * Resgatar o SkinName
     *
     * @return string
     */
    public function getSkinName() {
    	return ($this->skinName);
    }

    /**
     * Definir o formSkinName
     *
     * @param string $valor
     */
    public function setFormSkinName($valor) {
    	$this->formSkinName	= $valor;
    }
    
    /**
     * Resgatar o formSkinName
     *
     * @return string
     */
    public function getFormSkinName() {
    	return ($this->formSkinName);
    }
    
    /**
     * Definir o Html Load
     *
     * @param string $valor
     */
    public function setDynHtmlLoad($valor) {
    	$this->dynHtmlLoad	= $valor;
    }
    
    /**
     * Resgatar o Html Load
     *
     * @return string
     */
    public function getDynHtmlLoad() {
    	return ($this->dynHtmlLoad);
    }
    
	/**
	 * Resgatar os Estados
     */
    public function DBGetEstados() {
    	global $db;
    	return (
    		$db->extraiTodos("
	    		SELECT	*
	    		FROM	ESTADOS
    		")
    	);
    }
    
	/**
	 * Resgatar os Estados
     */
    public function DBGetCidades($codEstado = null) {
    	global $db;
    	if ($codEstado != null) {
    		$where		= "WHERE codEstado = '".$codEstado."'";
    	}else{
    		$where		= "";
    	}
    	return (
    		$db->extraiTodos("
	    		SELECT	*
	    		FROM	CIDADES
	    		$where
    		")
    	);
    }

    
    
	/**
     * Salvar o contatos enviados.
     */
  	public function DBSalvaComentario($nome, $email, $ddd, $fone, $codEstado, $codCidade, $comentarios, $receberMail) {
    	try {
			$this->db->db->beginTransaction();
			$this->db->Executa("BEGIN INSERT INTO CONTATOS (nome, email, ddd, fone, codEstado, codCidade, comentarios, indDesejaRecMail, data, ip ) VALUES (:nome, :email, :ddd, :fone, :codEstado, :codCidade, :comentarios, :indDesejaRecMail); END;",
				array(':nome'=>$nome, ':email'=>$email, ':ddd'=>$ddd, ':fone'=>$fone, ':codEstado'=>$codEstado, ':codCidade'=>$codCidade, ':comentarios'=>$comentarios, ':indDesejaRecMail'=>$receberMail)
				);
			$this->db->db->commit();
				return null;
		}catch (Exception $e) {
			$this->db->db->rollback();
				return($e->getMessage());
		}
    }

	/**
     * Resgatar os planos de assinatura do sistema
     */
    public function DBGetPlanos() {
    	global $db;
    	return (
    		$db->extraiTodos("
	    		SELECT	*
    			FROM	PLANOS
   			")
   		);
    }

    /**
     * Resgata o tipo de Usuário
     *
     * @param string $skin
     * @return string
     */
    public function DBGetTipoUsuario($usuario) {
    	global $db;
    	$return = $db->extraiPrimeiro("
				SELECT codTipo
				FROM   USUARIOS U
				WHERE  U.usuario		= '".$usuario."'
		");
   		if (isset($return->codTipo)) {
   			return ($return->codTipo);
   		}else{
   			return null;
   		}
    }

    /**
     * Regatar um parâmetro
     *
     * @param string $parametro
     * @return string
     */
    public function DBGetParametro ($parametro) {
    	global $db;
    	$return = $db->extraiPrimeiro("
	    		SELECT	valor
    			FROM	PARAMETROS P
    			WHERE	P.parametro = '".$parametro."'
   		");
   		
    	if (isset($return->valor)) {
    		return ($return->valor);
    	}else{
    		return null;
    	}
    }

    /**
     * Resgata as informações do Skin
     *
     * @param string $skin
     * @return string
     */
    public function DBGetInfoSkin($skin) {
    	global $db;
    	return (
    		$db->extraiPrimeiro("
	    		SELECT	*
    			FROM	SKINS S
				WHERE	S.codSkin	= '".$skin."'
   			")
   		);
    }


    /**
	 * Resgatar a lista de Tipos de Usuários
     */
    public function DBGetListTipoUsuario() {
    	global $db;
    	return (
    		$db->extraiTodos("
	    		SELECT	*
	    		FROM	TIPO_USUARIO
	    		ORDER BY descricao
    		")
    	);
    }
    
    /**
     * Resgata os Skins
     *
     * @return array
     */
    public function DBGetSkins() {
    	global $db;
    	return (
    		$db->extraiTodos("
	    		SELECT	*
    			FROM	SKINS S
   			")
   		);
    }

    /**
     * Gerar o html da localização do Menu
     *
     * @return string
     */
    public static function geraLocalizacao($codMenu,$codTipoUsuario) {
    	global $system;
    	
    	$aLocal         = \Menu::getArrayArvoreMenuUrl($codMenu);
    	$html           = '<div><ul class="breadcrumb">';
    	$html                   .= '<li><a href="#">Home</a></li>';
    	$total                  = sizeof($aLocal);
    	for ($i = 0; $i < sizeof($aLocal); $i++) {
    		if ($aLocal[$i]->link != null) {
    			$info		= \Usuarios::getInfo($system->getCodUsuario());
    			$codTipo	= $info->codTipo;
				$url = \Zage\Menu::montaUrl($aLocal[$i]->link, $aLocal[$i]->codMenu, $codTipo);
       		} else{
    			$url                    = "#";
    		}
    		$html  .= "<li><a href='".$url."'>".$aLocal[$i]->menu."</a>";
    		$html .= "</li>";
    	
    	}
    	$html   .= "</ul></div>";
    	return ($html);
    }
    
   /**
     * Gerar o xml para um objeto DHTMLXCOMBO
     *
     * @return string
     */
    public static function geraXmlCombo($array,$codigo,$valor,$codigoSel = null,$valorDefault = null) {
    	global $system;
		$xml	= '<?xml version="1.0" encoding="'.$system->config->charset.'"?><complete>';
		if ($valorDefault !== null) {
			($codigoSel == null) ? $selected = "selected=\"true\"" : $selected = "";
			$xml	.= "<option $selected value=\"\">".$valorDefault."</option>";
		}
		for($i=0; $i<sizeof($array);$i++) {
			if ($codigoSel !== null) {
				($codigoSel == $array[$i]->$codigo) ? $selected = "selected=\"true\"" : $selected = "";	
			}else{
				if ($i == 0) $selected = "selected=\"true\"";
			}
			$xml .= "<option value=\"".$array[$i]->$codigo."\" $selected>".$array[$i]->$valor.'</option>';
		}
		$xml	.= "</complete>";
		return ($xml);
    }
    
   /**
     * Gerar o xml para um objeto Select
     *
     * @return string
     */
    public static function geraXmlSelect($array,$codigo,$valor,$codigoSel = null,$valorDefault = null) {
    	global $system;
		$sel	= '';
		if ($valorDefault !== null) {
			($codigoSel == null) ? $selected = "selected=\"true\"" : $selected = "";
			$sel	.= "<option $selected value=\"\" text=\"".$valorDefault."\"/>";
		}
		for($i=0; $i<sizeof($array);$i++) {
			if ($codigoSel != null) {
				($codigoSel == $array[$i]->$codigo) ? $selected = "selected=\"true\"" : $selected = "";	
			}else{
				$selected = " ";
			}
			$sel .= "<option value=\"".$array[$i]->$codigo."\" $selected text=\"".$array[$i]->$valor."\"/>";
		}
		return ($sel);
    }
    
    
    /**
     * Resgatar o html padrão
     * @return string
     */
    public function loadHtml() {
    	return $this->getDynHtmlLoad();
    }
    
    /**
     * Retorna o conteudo do xml
     *
     * @param string $arquivo
     * @return string
     */
    public static function getXmlData ($arquivo) {
    	$xmlData	= \Zage\Util::getConteudoArquivo($arquivo);
    	$xmlData	= str_ireplace("\'", "\"", $xmlData);
    	$xmlData	= str_ireplace(PHP_EOL, null, $xmlData);
    	return($xmlData);
    }
    
    /**
     *
     * Procura por itens do tipo input em um Xml
     * @param string $xml
     * @return array $array
     */
    public static function getXmlInputs($xml) {
    	global $system;
    
    	if (substr($xml,0,5) == '<?xml') {
    		$xmlData	= $xml;
    	}else{
    		/** Coloca o arquivo XMl em uma string **/
    		$xmlData	= self::getXmlData($xml);
    	}
    
    	/** Ajusta o caracter set **/
    	$xmlData 	= str_ireplace("%CHARSET%", $system->config["charset"], $xmlData);
    
    	//$log->debug('XMLData: '.$xmlData);
    
    	/** Carrega o Arquivo Xml em um objeto **/
    	$xmlObj = simplexml_load_string($xmlData);
    
    	$inputs	= array();
    	DHCUtil::getXmlInputsItens($xmlObj,$inputs);
    	return ($inputs);
    
    }
    
    
    /**
     *
     * Procura por itens do tipo input em um Xml
     * @param string $xml
     * @return array $array
     */
    public static function getXmlInputsItens($obj,&$array) {
    	global $system;
    	foreach ($obj as $key => $value) {
    		if (is_object($value) && isset($value->attributes()->type)) {
    			switch ($value->attributes()->type) {
    				case "checkbox":
    				case "input":
    				case "file":
    				case "hidden":
    				case "multiselect":
    				case "password":
    				case "radio":
    				case "select":
    				case "textarea":
    				case "calendar":
    				case "colorpicker":
    				case "combo":
    				case "editor":
    					if (isset($value->attributes()->name) && isset($value->attributes()->className) && preg_match('#MCMask-(.+)#', $value->attributes()->className)) {
    						$nome 	= $value->attributes()->name;
    						//$log->debug("Name = ".$nome);
    						$array["$nome"]	= $value->attributes();
    					}
    					break;
    			}
    		}
    
    		/** Recursividade **/
    		self::getXmlInputsItens($value,$array);
    	}
    
    }
    
    /**
     * Verifica se o usuário tem permissão no sistema
     */
    public function ehAdmin($usuario) {
    	global $db;
    	$arr = $db->extraiPrimeiro("
			SELECT	COUNT(*) NUM
			FROM	USUARIOS U
			WHERE	U.usuario               = '".$usuario."'
			AND		U.codTipo               = 'A'
		");
    	if ($arr->NUM == 0) {
    		return false;
    	}else{
    		return true;
    	}
    }
    

}