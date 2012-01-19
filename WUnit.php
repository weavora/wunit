<?php

use WUnit\HttpKernel\Client;
use WUnit\Http\YiiKernel;

class WUnit extends CComponent
{

	private static $config = array();

	public function init()
	{
		// @todo what with 'header already sent' error?
		error_reporting(E_ERROR);

		// @todo implement autoloader
		$basePath = dirname(__FILE__);
		$files = array_merge(
			array(
				$basePath . '/CssSelector/Node/NodeInterface.php',
				$basePath . '/HttpFoundation/Request.php',
				$basePath . '/HttpFoundation/ParameterBag.php',
				$basePath . '/HttpFoundation/Response.php',
				$basePath . '/HttpFoundation/RequestMatcherInterface.php',
				$basePath . '/HttpFoundation/File/Exception/FileException.php',
				$basePath . '/HttpFoundation/SessionStorage/SessionStorageInterface.php',
				$basePath . '/HttpFoundation/SessionStorage/NativeSessionStorage.php',
				$basePath . '/DomCrawler/Link.php',
				$basePath . '/DomCrawler/Field/FormField.php',
				$basePath . '/Process/Process.php',
				$basePath . '/WUnitTestCase.php',
			),
			glob($basePath . '/BrowserKit/*.php'),
			glob($basePath . '/CssSelector/*.php'),
			glob($basePath . '/CssSelector/Exception/*.php'),
			glob($basePath . '/CssSelector/Node/*.php'),
			glob($basePath . '/DomCrawler/*.php'),
			glob($basePath . '/DomCrawler/Field/*.php'),
			glob($basePath . '/HttpKernel/*.php'),
			glob($basePath . '/HttpFoundation/*.php'),
			glob($basePath . '/HttpFoundation/File/Exception/*.php'),
			glob($basePath . '/HttpFoundation/File/MineType/*.php'),
			glob($basePath . '/HttpFoundation/File/*.php'),
			glob($basePath . '/HttpFoundation/SessionStorage/*.php'),
			glob($basePath . '/HttpFoundation/*.php'),
			glob($basePath . '/Http/*.php'),
			glob($basePath . '/Process/*.php')
		);

		
		foreach($files as $file) {
			require_once($file);
		}
	}

	public function createClient()
	{
		$client  = new Client(new YiiKernel());
		return $client;
	}

	public static function createWebApplication($config = null)
	{
		if ($config !== null)
			self::$config = $config;

		$basePath = dirname(__FILE__);
		require_once($basePath . '/Http/YiiApplication.php');
		require_once($basePath . '/PHPUnit/ResultPrinter.php');
		return new YiiApplication(self::$config);
	}
}