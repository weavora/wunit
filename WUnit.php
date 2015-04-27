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
	}

	public function createClient()
	{
		$client = new Client(new YiiKernel());
		return $client;
	}

	public static function createWebApplication($config = null)
	{
		if ($config !== null) {
			self::$config = $config;
		}

		return new YiiApplication(self::$config);
	}
}
