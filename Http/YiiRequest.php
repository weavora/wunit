<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 9/13/11
 * Time: 1:32 PM
 * To change this template use File | Settings | File Templates.
 */

namespace WUnit\Http;

class YiiRequest extends \CHttpRequest {
	/**
	 * @var boolean whether cookies should be validated to ensure they are not tampered. Defaults to false.
	 */
	public $enableCookieValidation=false;
	/**
	 * @var boolean whether to enable CSRF (Cross-Site Request Forgery) validation. Defaults to false.
	 * By setting this property to true, forms submitted to an Yii Web application must be originated
	 * from the same application. If not, a 400 HTTP exception will be raised.
	 * Note, this feature requires that the user client accepts cookie.
	 * You also need to use {@link CHtml::form} or {@link CHtml::statefulForm} to generate
	 * the needed HTML forms in your pages.
	 * @see http://seclab.stanford.edu/websec/csrf/csrf.pdf
	 */
	public $enableCsrfValidation=false;
	/**
	 * @var string the name of the token used to prevent CSRF. Defaults to 'YII_CSRF_TOKEN'.
	 * This property is effectively only when {@link enableCsrfValidation} is true.
	 */
	public $csrfTokenName='YII_CSRF_TOKEN';
	/**
	 * @var array the property values (in name-value pairs) used to initialize the CSRF cookie.
	 * Any property of {@link CHttpCookie} may be initialized.
	 * This property is effective only when {@link enableCsrfValidation} is true.
	 */
	public $csrfCookie;

	private $_requestUri;
	private $_pathInfo;
	private $_scriptFile;
	private $_scriptUrl;
	private $_hostInfo;
	private $_baseUrl;
	private $_cookies;
	private $_preferredLanguage;
	private $_csrfToken;
	private $_deleteParams;
	private $_putParams;

	private $_getParams;
	private $_postParams;
	private $_serverParams;

	/**
	 * Initializes the application component.
	 * This method overrides the parent implementation by preprocessing
	 * the user request data.
	 */
	public function init()
	{
		parent::init();
	}

	public function inject($getParams, $postParams, $serverParams)
	{
		$this->_getParams = $_GET = $getParams;
		$this->_postParams = $_POST = $postParams;
		$this->_serverParams = $serverParams;

		if (empty($this->_serverParams['PHP_SELF'])) {
			$this->_serverParams['PHP_SELF'] = '/index.php';
		}
		
		if (empty($this->_serverParams['SCRIPT_FILENAME'])) {
			$this->_serverParams['SCRIPT_FILENAME'] = \Yii::getPathOfAlias('application') . '/../index.php';
		}

		$_SERVER = $this->_serverParams;
		$_REQUEST = array_merge($_GET, $_POST);

		parent::init();
	}

	/**
	 * Normalizes the request data.
	 * This method strips off slashes in request data if get_magic_quotes_gpc() returns true.
	 * It also performs CSRF validation if {@link enableCsrfValidation} is true.
	 */
	protected function normalizeRequest()
	{
		if($this->enableCsrfValidation)
			Yii::app()->attachEventHandler('onBeginRequest',array($this,'validateCsrfToken'));
	}

	/**
	 * Returns the named GET or POST parameter value.
	 * If the GET or POST parameter does not exist, the second parameter to this method will be returned.
	 * If both GET and POST contains such a named parameter, the GET parameter takes precedence.
	 * @param string $name the GET parameter name
	 * @param mixed $defaultValue the default parameter value if the GET parameter does not exist.
	 * @return mixed the GET parameter value
	 * @since 1.0.4
	 * @see getQuery
	 * @see getPost
	 */
	public function getParam($name,$defaultValue=null)
	{
		return isset($this->_getParams[$name]) ? $this->_getParams[$name] : (isset($this->_postParams[$name]) ? $this->_postParams[$name] : $defaultValue);
	}

	/**
	 * Returns the named GET parameter value.
	 * If the GET parameter does not exist, the second parameter to this method will be returned.
	 * @param string $name the GET parameter name
	 * @param mixed $defaultValue the default parameter value if the GET parameter does not exist.
	 * @return mixed the GET parameter value
	 * @since 1.0.4
	 * @see getPost
	 * @see getParam
	 */
	public function getQuery($name,$defaultValue=null)
	{
		return isset($this->_getParams[$name]) ? $this->_getParams[$name] : $defaultValue;
	}

	/**
	 * Returns the named POST parameter value.
	 * If the POST parameter does not exist, the second parameter to this method will be returned.
	 * @param string $name the POST parameter name
	 * @param mixed $defaultValue the default parameter value if the POST parameter does not exist.
	 * @return mixed the POST parameter value
	 * @since 1.0.4
	 * @see getParam
	 * @see getQuery
	 */
	public function getPost($name,$defaultValue=null)
	{
		return isset($this->_postParams[$name]) ? $this->_postParams[$name] : $defaultValue;
	}

	/**
	 * Returns the schema and host part of the application URL.
	 * The returned URL does not have an ending slash.
	 * By default this is determined based on the user request information.
	 * You may explicitly specify it by setting the {@link setHostInfo hostInfo} property.
	 * @param string $schema schema to use (e.g. http, https). If empty, the schema used for the current request will be used.
	 * @return string schema and hostname part (with port number if needed) of the request URL (e.g. http://www.yiiframework.com)
	 * @see setHostInfo
	 */
	public function getHostInfo($schema='')
	{
		if($this->_hostInfo===null)
		{
			if($secure=$this->getIsSecureConnection())
				$http='https';
			else
				$http='http';
			if(isset($this->_serverParams['HTTP_HOST']))
				$this->_hostInfo=$http.'://'.$this->_serverParams['HTTP_HOST'];
			else
			{
				$this->_hostInfo=$http.'://'.$this->_serverParams['SERVER_NAME'];
				$port=$secure ? $this->getSecurePort() : $this->getPort();
				if(($port!==80 && !$secure) || ($port!==443 && $secure))
					$this->_hostInfo.=':'.$port;
			}
		}
		if($schema!=='')
		{
			$secure=$this->getIsSecureConnection();
			if($secure && $schema==='https' || !$secure && $schema==='http')
				return $this->_hostInfo;

			$port=$schema==='https' ? $this->getSecurePort() : $this->getPort();
			if($port!==80 && $schema==='http' || $port!==443 && $schema==='https')
				$port=':'.$port;
			else
				$port='';

			$pos=strpos($this->_hostInfo,':');
			return $schema.substr($this->_hostInfo,$pos,strcspn($this->_hostInfo,':',$pos+1)+1).$port;
		}
		else
			return $this->_hostInfo;
	}

	/**
	 * Returns the relative URL of the entry script.
	 * The implementation of this method referenced Zend_Controller_Request_Http in Zend Framework.
	 * @return string the relative URL of the entry script.
	 */
	public function getScriptUrl()
	{
		if($this->_scriptUrl===null)
		{
			
			$scriptName=basename($this->_serverParams['SCRIPT_FILENAME']);
			if(basename($this->_serverParams['SCRIPT_NAME'])===$scriptName)
				$this->_scriptUrl=$this->_serverParams['SCRIPT_NAME'];
			else if(basename($this->_serverParams['PHP_SELF'])===$scriptName)
				$this->_scriptUrl=$this->_serverParams['PHP_SELF'];
			else if(isset($this->_serverParams['ORIG_SCRIPT_NAME']) && basename($this->_serverParams['ORIG_SCRIPT_NAME'])===$scriptName)
				$this->_scriptUrl=$this->_serverParams['ORIG_SCRIPT_NAME'];
			else if(($pos=strpos($this->_serverParams['PHP_SELF'],'/'.$scriptName))!==false)
				$this->_scriptUrl=substr($this->_serverParams['SCRIPT_NAME'],0,$pos).'/'.$scriptName;
			else if(isset($this->_serverParams['DOCUMENT_ROOT']) && strpos($this->_serverParams['SCRIPT_FILENAME'],$this->_serverParams['DOCUMENT_ROOT'])===0)
				$this->_scriptUrl=str_replace('\\','/',str_replace($this->_serverParams['DOCUMENT_ROOT'],'',$this->_serverParams['SCRIPT_FILENAME']));
			else
				throw new CException(Yii::t('yii','CHttpRequest is unable to determine the entry script URL.'));
		}
		return $this->_scriptUrl;
	}


	/**
	 * Returns the path info of the currently requested URL.
	 * This refers to the part that is after the entry script and before the question mark.
	 * The starting and ending slashes are stripped off.
	 * @return string part of the request URL that is after the entry script and before the question mark.
	 * Note, the returned pathinfo is decoded starting from 1.1.4.
	 * Prior to 1.1.4, whether it is decoded or not depends on the server configuration
	 * (in most cases it is not decoded).
	 * @throws CException if the request URI cannot be determined due to improper server configuration
	 */
	public function getPathInfo()
	{
		if($this->_pathInfo===null)
		{
			$pathInfo=$this->getRequestUri();

			if(($pos=strpos($pathInfo,'?'))!==false)
			   $pathInfo=substr($pathInfo,0,$pos);

			$pathInfo=urldecode($pathInfo);

			$scriptUrl=$this->getScriptUrl();
			$baseUrl=$this->getBaseUrl();
			if(strpos($pathInfo,$scriptUrl)===0)
				$pathInfo=substr($pathInfo,strlen($scriptUrl));
			else if($baseUrl==='' || strpos($pathInfo,$baseUrl)===0)
				$pathInfo=substr($pathInfo,strlen($baseUrl));
			else if(strpos($this->_serverParams['PHP_SELF'],$scriptUrl)===0)
				$pathInfo=substr($this->_serverParams['PHP_SELF'],strlen($scriptUrl));
			else
				throw new CException(Yii::t('yii','CHttpRequest is unable to determine the path info of the request.'));

			$this->_pathInfo=trim($pathInfo,'/');
		}
		return $this->_pathInfo;
	}

	/**
	 * Returns the request URI portion for the currently requested URL.
	 * This refers to the portion that is after the {@link hostInfo host info} part.
	 * It includes the {@link queryString query string} part if any.
	 * The implementation of this method referenced Zend_Controller_Request_Http in Zend Framework.
	 * @return string the request URI portion for the currently requested URL.
	 * @throws CException if the request URI cannot be determined due to improper server configuration
	 * @since 1.0.1
	 */
	public function getRequestUri()
	{
		if($this->_requestUri===null)
		{
			if(isset($this->_serverParams['HTTP_X_REWRITE_URL'])) // IIS
				$this->_requestUri=$this->_serverParams['HTTP_X_REWRITE_URL'];
			else if(isset($this->_serverParams['REQUEST_URI']))
			{
				$this->_requestUri=$this->_serverParams['REQUEST_URI'];
				if(isset($this->_serverParams['HTTP_HOST']))
				{
					if(strpos($this->_requestUri,$this->_serverParams['HTTP_HOST'])!==false)
						$this->_requestUri=preg_replace('/^\w+:\/\/[^\/]+/','',$this->_requestUri);
				}
				else
					$this->_requestUri=preg_replace('/^(http|https):\/\/[^\/]+/i','',$this->_requestUri);
			}
			else if(isset($this->_serverParams['ORIG_PATH_INFO']))  // IIS 5.0 CGI
			{
				$this->_requestUri=$this->_serverParams['ORIG_PATH_INFO'];
				if(!empty($this->_serverParams['QUERY_STRING']))
					$this->_requestUri.='?'.$this->_serverParams['QUERY_STRING'];
			}
			else
				throw new CException(Yii::t('yii','CHttpRequest is unable to determine the request URI.'));
		}

		return $this->_requestUri;
	}

	/**
	 * Returns part of the request URL that is after the question mark.
	 * @return string part of the request URL that is after the question mark
	 */
	public function getQueryString()
	{
		return isset($this->_serverParams['QUERY_STRING'])?$this->_serverParams['QUERY_STRING']:'';
	}

	/**
	 * Return if the request is sent via secure channel (https).
	 * @return boolean if the request is sent via secure channel (https)
	 */
	public function getIsSecureConnection()
	{
		return isset($this->_serverParams['HTTPS']) && !strcasecmp($this->_serverParams['HTTPS'],'on');
	}

	/**
	 * Returns the request type, such as GET, POST, HEAD, PUT, DELETE.
	 * @return string request type, such as GET, POST, HEAD, PUT, DELETE.
	 */
	public function getRequestType()
	{
		return strtoupper(isset($this->_serverParams['REQUEST_METHOD'])?$this->_serverParams['REQUEST_METHOD']:'GET');
	}

	/**
	 * Returns whether this is a POST request.
	 * @return boolean whether this is a POST request.
	 */
	public function getIsPostRequest()
	{
		return isset($this->_serverParams['REQUEST_METHOD']) && !strcasecmp($this->_serverParams['REQUEST_METHOD'],'POST');
	}

	/**
	 * Returns whether this is a DELETE request.
	 * @return boolean whether this is a DELETE request.
	 * @since 1.1.7
	 */
	public function getIsDeleteRequest()
	{
		return isset($this->_serverParams['REQUEST_METHOD']) && !strcasecmp($this->_serverParams['REQUEST_METHOD'],'DELETE');
	}

	/**
	 * Returns whether this is a PUT request.
	 * @return boolean whether this is a PUT request.
	 * @since 1.1.7
	 */
	public function getIsPutRequest()
	{
		return isset($this->_serverParams['REQUEST_METHOD']) && !strcasecmp($this->_serverParams['REQUEST_METHOD'],'PUT');
	}

	/**
	 * Returns whether this is an AJAX (XMLHttpRequest) request.
	 * @return boolean whether this is an AJAX (XMLHttpRequest) request.
	 */
	public function getIsAjaxRequest()
	{
		return isset($this->_serverParams['HTTP_X_REQUESTED_WITH']) && $this->_serverParams['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
	}

	/**
	 * Returns the server name.
	 * @return string server name
	 */
	public function getServerName()
	{
		return $this->_serverParams['SERVER_NAME'];
	}

	/**
	 * Returns the server port number.
	 * @return integer server port number
	 */
	public function getServerPort()
	{
		return $this->_serverParams['SERVER_PORT'];
	}

	/**
	 * Returns the URL referrer, null if not present
	 * @return string URL referrer, null if not present
	 */
	public function getUrlReferrer()
	{
		return isset($this->_serverParams['HTTP_REFERER'])?$this->_serverParams['HTTP_REFERER']:null;
	}

	/**
	 * Returns the user agent, null if not present.
	 * @return string user agent, null if not present
	 */
	public function getUserAgent()
	{
		return isset($this->_serverParams['HTTP_USER_AGENT'])?$this->_serverParams['HTTP_USER_AGENT']:null;
	}

	/**
	 * Returns the user IP address.
	 * @return string user IP address
	 */
	public function getUserHostAddress()
	{
		return isset($this->_serverParams['REMOTE_ADDR'])?$this->_serverParams['REMOTE_ADDR']:'127.0.0.1';
	}

	/**
	 * Returns the user host name, null if it cannot be determined.
	 * @return string user host name, null if cannot be determined
	 */
	public function getUserHost()
	{
		return isset($this->_serverParams['REMOTE_HOST'])?$this->_serverParams['REMOTE_HOST']:null;
	}

	/**
	 * Returns entry script file path.
	 * @return string entry script file path (processed w/ realpath())
	 */
	public function getScriptFile()
	{
		if($this->_scriptFile!==null)
			return $this->_scriptFile;
		else
			return $this->_scriptFile=realpath($this->_serverParams['SCRIPT_FILENAME']);
	}


	/**
	 * Returns user browser accept types, null if not present.
	 * @return string user browser accept types, null if not present
	 */
	public function getAcceptTypes()
	{
		return isset($this->_serverParams['HTTP_ACCEPT'])?$this->_serverParams['HTTP_ACCEPT']:null;
	}



	/**
	 * Redirects the browser to the specified URL.
	 * @param string $url URL to be redirected to. If the URL is a relative one, the base URL of
	 * the application will be inserted at the beginning.
	 * @param boolean $terminate whether to terminate the current application
	 * @param integer $statusCode the HTTP status code. Defaults to 302. See {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html}
	 * for details about HTTP status code. This parameter has been available since version 1.0.4.
	 */
	public function redirect($url,$terminate=true,$statusCode=302)
	{
		if(strpos($url,'/')===0)
			$url=$this->getHostInfo().$url;
		header('Location: '.$url, true, $statusCode);
//		if($terminate)
//			\Yii::app()->end();
	}

}


