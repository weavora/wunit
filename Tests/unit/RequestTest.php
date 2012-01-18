<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class RequestTest extends \PHPUnit_Framework_TestCase
{

	protected static $wunit;

	public static function setUpBeforeClass()
	{
		static::$wunit = new WUnit();
		static::$wunit->init();
	}

	public function testCreateClient()
	{
		$client = static::$wunit->createClient();
		$this->assertNotEmpty($client);
	}

	public function testUrlManagerFormat()
	{
		$client = static::$wunit->createClient();

		Yii::app()->urlManager->urlFormat = CUrlManager::PATH_FORMAT;
		$crawler = $client->request('GET', '/test/index');
		$this->assertNotEmpty($client->getResponse()->getContent());

		Yii::app()->urlManager->urlFormat = CUrlManager::GET_FORMAT;
		$crawler = $client->request('GET', '/index.php', array('r' => '/test/index'));
		$this->assertNotEmpty($client->getResponse()->getContent());

//		Yii::app()->urlManager->urlFormat = 'path';

	}
}
