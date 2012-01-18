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

	public function init()
	{
		parent::init();
	}

	public function inject($getParams, $postParams, $serverParams)
	{
		$_GET = $getParams;
		$_POST = $postParams;
		$_SERVER = $serverParams;

		if (empty($_SERVER['PHP_SELF'])) {
			$_SERVER['PHP_SELF'] = '/index.php';
		}
		
		if (empty($_SERVER['SCRIPT_FILENAME'])) {
			$_SERVER['SCRIPT_FILENAME'] = \Yii::getPathOfAlias('application') . '/../index.php';
		}

		$_REQUEST = array_merge($_GET, $_POST);
	}

	protected function normalizeRequest()
	{
		if($this->enableCsrfValidation)
			Yii::app()->attachEventHandler('onBeginRequest',array($this, 'validateCsrfToken'));
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


