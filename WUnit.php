<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

use WUnit\HttpKernel\Client;
use WUnit\Http\YiiKernel;
use WUnit\Http\YiiExitException;
use WUnit\Http\YiiApplication;

class WUnit extends CComponent
{

	private static $config = array();
	private $_basePath = null;

	public function init()
	{
		// @todo what with 'header already sent' error?
		error_reporting(E_ERROR);

		$this->_basePath = dirname(__FILE__);

		spl_autoload_register(array($this, 'autoload'));
	}

	public function createClient()
	{
		$client = new Client(new YiiKernel());
		return $client;
	}

	public static function createWebApplication($config = null)
	{
		if ($config !== null)
			self::$config = $config;

		$basePath = dirname(__FILE__);
		require_once($basePath . '/Http/YiiApplication.php');
		require_once($basePath . '/UploadedFile.php');
		require_once($basePath . '/PHPUnit/ResultPrinter.php');
		return new YiiApplication(self::$config);
	}

	private function autoload($className)
	{
		$className = str_replace("Symfony\\Component\\", "", $className);
		$className = str_replace("WUnit\\", "", $className);

		$filePath = $this->_basePath . DIRECTORY_SEPARATOR . $className . ".php";

		if (!file_exists($filePath))
			return false;

		require_once $filePath;
	}

}