<?php

use WUnit\HttpKernel\Client;
use WUnit\Http\YiiKernel;
use WUnit\HttpFoundation\Request;

class WUnit extends CComponent
{

	public function init()
	{
		// @todo what with 'header already sent' error?
		error_reporting(E_ERROR);

		// @todo implement autoloader
		$basePath = Yii::getPathOfAlias('application.tests.components.wunit');
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
}