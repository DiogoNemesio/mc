<?php

/**
 * Classe que implementa as funções do Sistema
 * 
 * @package: SystemGCL
 * @created: 20/10/2010
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class SystemMC {

	/**
	 * Instância da classe DHCUsuario
	 *
	 * @var object
	 */
	public $usuario;
	
	/**
	 * Instância da classe Zend_Mail
	 *
	 * @var object
	 */
	public $mail;

	/**
	 * Instância da classe DHCLog
	 *
	 * @var object
	 */
	public $log;

	/**
	 * Instância da classe DHCConfig
	 *
	 * @var object
	 */
	public $config;
	
	/**
	 * Instância da classe DHCConexaoBanco
	 *
	 * @var object
	 */
	public $db;

	/**
	 * Instância da classe MCMascara
	 *
	 * @var object
	 */
	public $mask;
	
	/**
	 * Indica se o usuário ja está autenticado
	 *
	 * @var boolean
	 */
	public $autenticado;
	
	/**
	 * Indica se ja foi feita a reconexão com o novo usuário de banco
	 *
	 * @var boolean
	 */
	private $reconectado;

	/**
	 * Código do Sistema
	 *
	 * @var boolean
	 */
	private $codSistema;
	
	/**
	 * Código do Módulo
	 *
	 * @var boolean
	 */
	private $codModulo;

	/**
     * Construtor
     *
	 * @return void
	 */
	public function __construct() {

    	/** Instânciando os objetos **/
		$this->config		= new Zend_Config_Ini(CONFIG_FILE, CONFIG_SESSION);

		/** Definindo o timezone padrão **/
		date_default_timezone_set($this->config->data->timezone);
		
    	/** Instânciando os objetos **/
		$this->mail			= new Zend_Mail('utf-8');
		$this->initLog();

		$this->log->debug->debug(__CLASS__.": nova Instância !!!");

		
		/** Não mostrar os erros **/
		ini_set("display_errors", "1");
		
		/** Definindo atributos globais a Instância de e-mail (Podem ser alterados no momento do envio do e-mail) **/
		$this->mail->setFrom($this->config->mail->from,$this->config->mail->fromname);
		$this->mail->addTo($this->config->admin->email,$this->config->admin->nome);

		/** Fazendo a conexão ao banco de dados **/
		$this->db		= DHCConexaoBanco::init();
		$this->db->conectar(null,null,null,null,DHC_SENHA_ESCONDIDA);

		/** Instânciando os objetos por ordem de precedência **/
		$this->usuario		= new DHCUsuario();
		
		/** Define que o usuário não está autenticado **/
		$this->desautentica();
		
		/** Define que o usuário ainda não reconectou com o banco de dados **/
		$this->reconectado = false;
		
		/** Carrega as configurações de máscara **/
		$this->mask			= new MCMascara();
		
    }
	
    /**
     * Resgatar o Usuário
     *
     * @return string
     */
    public function getUsuario () {
    	if (is_object($this->usuario)) {
    		return $this->usuario->getUsuario();
    	}else{
    		return null;
    	}
    }
    
    /**
     * Indicar que o usuário está autenticado
     */
    public function setAutenticado() {
    	$this->autenticado = 1;
    }

    /**
     * Desautenticar
     *
     */
    public function desautentica() {
    	$this->autenticado = 0;
    }

    /**
     * Verifica se o usuario ja está autenticado
     *
     * @return boolean
     */
    public function estaAutenticado() {
    	return $this->autenticado;
    }

    /**
     * Definir o código do Sistema
     */
    public function setCodSistema($valor) {
    	$this->codSistema	= $valor;
    }

    /**
     * Resgatar o código do Sistema
     */
    public function getCodSistema() {
    	return($this->codSistema);
    }

    /**
     * Definir o código do Sistema
     */
    public function setCodModulo($valor) {
    	$this->codModulo	= $valor;
    }

    /**
     * Resgatar o código do Sistema
     */
    public function getCodModulo() {
    	return($this->codModulo);
    }
    
    /**
     * Verifica se o usuario ja está autenticado
     *
     * @return boolean
     */
    public function jaReconectou() {
    	return $this->reconectado;
    }
    
    /**
     * Inicia o streamer de log 
     *
     * @return boolean
     */
    public function initLog() {
    	return $this->log = DHCLog::init();
    }
    
    /**
     * Terminar a execução do script por conta de um erro, se for o caso tb mandar um e-mail
     *
     * @param string $mensagem
     * @param string $trace
     */
    public function halt ($mensagem, $trace = false, $classe = false, $mostrar = false) {

    	/** Gerar Log de Erro **/
		$this->log->file->err("$classe: ".$mensagem);
		
		$htmlMessage	= "
		<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"[url=\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\"]http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd[/url]\">
		<html xml:lang=\"en\" lang=\"en\" xmlns=\"http://www.w3.org/1999/xhtml\">
		<meta http-equiv=\"content-type\" content=\"text/html; charset=".$this->config->charset."\" />
		<head>
		<style type=text/css>
		.Texto {
			font-family: Trebuchet MS,Verdana,Arial;
			font-size: 14px;
		}
		.Mensagem {
			font-family: Trebuchet MS,Verdana,Arial;
			font-size: 16px;
			color: Red;
		}

		.Titulo {
			font-family: Trebuchet MS,Verdana,Arial;
			font-weight: bold;
			font-size: 16px;
		}
		</style>
		<body align='center'>
		<table align='center'>
		";
		if ($classe) {
			$htmlMessage .= "<tr><td class='Titulo'>Classe:</td><td class='Texto'>".$classe."</td></tr>";
		}
		if ($trace) {
			$htmlMessage .= "<tr><td class='Titulo'>Trace:</td><td class='Texto'>".$trace."</td></tr>";
		}
		if ($this->getUsuario()) {
			$htmlMessage .= "<tr><td class='Titulo'>Usuário:</td><td class='Texto'>".$this->getUsuario()."</td></tr>";
		}

		$htmlMessage .= "<tr><td class='Titulo'>IP:</td><td class='Texto'>".$_SERVER['REMOTE_ADDR']."</td></tr>";
		$htmlMessage .= "<tr><td class='Titulo'>Erro:</td><td class='Mensagem'>".$mensagem."</td></tr>
		</table>
		</body>
		</html>";
		
		/** Enviar e-mail se o parâmetro estiver configurado para isso **/
		if ($this->config->admin->email) {
			$this->mail->setBodyHtml($htmlMessage);
			$this->mail->send();
		}

		/** Terminar o script **/
		if ($mostrar) {
			DHCErro::halt($htmlMessage);
		}else{
			DHCErro::halt();
		}
    }

    /**
     * Terminar a execução do script por conta de um erro, se for o caso tb mandar um e-mail
     *
     * @param string $mensagem
     * @param string $trace
     */
    public function avisa ($mensagem,$halt = false) {

    	/** Gerar Log de Erro **/
		$this->log->file->warn($mensagem);
		
		echo "<script>alert('".$mensagem."');</scrip>";

		/** Terminar o script **/
		if ($halt) {
			exit;
		}
    }

   /**
    * Desconecta do banco
    */
    public function DBDesconecta () {
    	$this->db->desconectar();
    	$this->log->debug->debug(__CLASS__.": Conexão fechada !!!");
    }
    
    /**
    * Reconecta no banco
    */
    public function DBReconecta ($usuario,$senha,$banco) {
    	$this->db->conectar('',$usuario,$senha,$banco,DHC_SENHA_NAO_ESCONDIDA);
    	$this->reconectado = true;
    	$this->log->debug->debug(__CLASS__.": Reconectou com usuario novo (".$usuario.") !!!");
    }
    
    
    /**
     * Verifica se o usuário tem permissão no sistema
     */
    public function ehAdmin($usuario) {
    	$arr = $this->db->extraiPrimeiro("
			SELECT	COUNT(*) NUM
			FROM	USUARIOS U
			WHERE	U.usuario		= '".$usuario."'
			AND		U.codTipo		= 'A'
		");
    	if ($arr->NUM == 0) {
    		return false;
    	}else{
    		return true;
    	}
    }

    /**
     * Verifica se o usuário tem permissão no módulo
     */
    public function temPermissaoNoModulo() {
    	$arr = $this->db->extraiPrimeiro("
	    	SELECT	COUNT(*) NUM
    		FROM	GCL_VW_ACESSO_USUARIO_MODULO AUM
			WHERE	AUM.USUARIO		= '".$this->getUsuario()."'
			AND		AUM.COD_MODULO	= '".$this->getCodModulo()."'");
    	
    	if ($arr->NUM == 0) {
    		return false;
    	}else{
    		return true;
    	}
    }

    /**
     * Resgata os módulos que os usuários tem acesso
     */
    public function DBGetUsuarioModulos($codSistema = '') {
    	
    	if (!$codSistema) $codSistema = $this->getCodSistema();
    	
    	return($this->db->extraiTodos("
			SELECT	M.*
			FROM	GCL_VW_ACESSO_USUARIO_MODULO AUM,
					GCL_MODULOS M
			WHERE	AUM.USUARIO			= '".$this->getUsuario()."'
			AND		AUM.COD_SISTEMA		= '".$codSistema."'
			AND		AUM.COD_MODULO		= M.COD_MODULO
			ORDER	BY M.COD_SISTEMA,M.COD_MODULO
			"));
    }

    /**
     * Resgata os Sistemas que os usuários tem acesso
     */
    public function DBGetUsuarioSistemas($codFilial = '') {
    	if ($codFilial) {
    		$codFilial = "AND		FM.COD_FILIAL               = '".$codFilial."'";
    	}
    	
    	return($this->db->extraiTodos("
			SELECT	DISTINCT S.COD_SISTEMA,S.SISTEMA
			FROM	GCL_VW_ACESSO_US_FIL_MODULO FM,
					GCL_MODULOS                 M,
					GCL_SISTEMAS                S
			WHERE	FM.COD_MODULO               = M.COD_MODULO
			AND		M.COD_SISTEMA               = S.COD_SISTEMA
			AND		FM.USUARIO                  = '".$this->getUsuario()."'
			$codFilial			
		"));
    }

    
    /**
     * Resgata o nome do módulo
     */
    public function DBGetNomeModulo($codModulo) {
    	
    	$arr	= $this->db->extraiPrimeiro("
			SELECT	M.MODULO
			FROM	GCL_MODULOS M
			WHERE	M.COD_MODULO		= '".$codModulo."'"
		);
		
		return ($arr->MODULO);
    }
    
    /**
     * Resgata as filiais que o usuário tem acesso em um módulo específico
     */
    public function DBGetFiliaisAcessoModulo($codModulo) {
    	return($this->db->extraiTodos("
			SELECT FM.COD_FILIAL,E.NOME,E.NOM_FANTASIA FANTASIA
			FROM   GCL_VW_ACESSO_US_FIL_MODULO FM,
			       EMPRESA E
			WHERE  FM.COD_FILIAL            = E.EMPRESA
			AND    FM.USUARIO           	= '".$this->getUsuario()."'
			AND    FM.COD_MODULO            = '".$codModulo."'
			ORDER  BY FM.COD_FILIAL,E.NOM_FANTASIA,E.NOME
			")
		);
	}

    /**
     * Resgata as filiais que o usuário tem acesso
     */
    public function DBGetEmpresasAcesso() {
    	return($this->db->extraiTodos("
			SELECT DISTINCT E.EMPRESA CODEMPRESA,E.NOME
			FROM   GCL_USUARIO_FILIAL	UF,
			       GCL_USUARIOS			U,
			       EMPRESA				F,
			       EMPRESA				E
			WHERE  UF.COD_FILIAL	= F.EMPRESA
			AND    UF.COD_USUARIO	= U.COD_USUARIO
			AND	   F.MATRIZ			= E.EMPRESA
			AND    U.USUARIO		= '".$this->getUsuario()."'"
		));
	}

	
	/**
     * Resgata as filiais que o usuário tem acesso
     */
    public function DBGetFiliaisAcesso($empresa = '') {
    	if ($empresa) {
    		$empresa	= "AND E.MATRIZ	= '".$empresa."'";
    	}

    	return($this->db->extraiTodos("
			SELECT DISTINCT E.EMPRESA CODFILIAL,E.NOME,E.NOM_FANTASIA FANTASIA
			FROM   GCL_USUARIO_FILIAL	UF,
			       GCL_USUARIOS			U,
			       EMPRESA				E
			WHERE  UF.COD_FILIAL	= E.EMPRESA
			AND    UF.COD_USUARIO	= U.COD_USUARIO
			AND    U.USUARIO		= '".$this->getUsuario()."'
    		$empresa
    		ORDER BY E.EMPRESA,E.NOME
		"));
	}

	/**
     * Resgata o código do sistema do módulo
     */
    public function getCodSistemaModulo($codModulo) {
    	$arr	=  $this->db->extraiPrimeiro("
			SELECT  M.COD_SISTEMA
			FROM    GCL_MODULOS M
			WHERE   M.COD_MODULO  = '".$codModulo."'
		");
    	return ($arr->COD_SISTEMA);
	}
	
	/**
     * Verifica se o usuário tem determinado direito
     */
    public function temDireito($codFilial,$codDireito) {
    	$arr	=  $this->db->extraiPrimeiro("
			SELECT	COUNT(*) NUM
			FROM	GCL_VW_USUARIO_DIREITOS UD,
					GCL_USUARIOS U
			WHERE	U.COD_USUARIO	= UD.COD_USUARIO
			AND		U.USUARIO		= '".$this->getUsuario()."'
			AND		UD.COD_FILIAL	= '".$codFilial."'
			AND		UD.COD_DIREITO	= '".$codDireito."'
		");
    	
    	if ($arr->NUM == 0) {
    		return false;
    	}else{
    		return true;
    	}
	}
	
	/**
	 * Resgatar o código do usuário
	 */
	public function getCodUsuario () {
		return($this->usuario->getCodUsuario());
	}
	
	/**
	 * Definir o tipo do usuário
	 */
	public function setCodUsuario ($tipo) {
		return($this->usuario->setCodUsuario($tipo));
	}
	
	/**
	 * Resgatar o tipo do usuário
	 */
	public function getTipoUsuario () {
		return($this->usuario->getTipo());
	}
	
	/**
	 * Definir o tipo do usuário
	 */
	public function setTipoUsuario ($tipo) {
		return($this->usuario->setTipo($tipo));
	}
}