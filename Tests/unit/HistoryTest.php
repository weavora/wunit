<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class HistoryTest extends \PHPUnit_Framework_TestCase
{

	protected static $wunit;

	public static function setUpBeforeClass()
	{
		static::$wunit = new WUnit();
		static::$wunit->init();
	}

	public function testNavigation()
	{
		$client = static::$wunit->createClient();
		$client->request('GET', '/test/first');
		$client->request('GET', '/test/second');
		$this->assertRegExp('/second/is', $client->getResponse()->getContent());

		$client->back();
		$this->assertRegExp('/first/is', $client->getResponse()->getContent());

		$client->forward();
		$this->assertRegExp('/second/is', $client->getResponse()->getContent());

		$client->reload();
		$this->assertRegExp('/second/is', $client->getResponse()->getContent());
	}

	/**
	 * @expectedException     LogicException
	 */
	public function testBack()
	{
		$client = static::$wunit->createClient();
		$client->back();
	}

	/**
	 * @expectedException     LogicException
	 */
	public function testRestart()
	{
		$client = static::$wunit->createClient();
		$client->request('GET', '/test/first');
		$client->request('GET', '/test/second');
		$client->restart();
		$client->back();
	}

	public function testCookies()
	{
		$client = static::$wunit->createClient();
		$client->request('GET', '/test/setCookies');
		$this->assertNotEmpty($client->getResponse()->headers->has('set-cookie'));

		$client->request('GET', '/test/getCookies');
		$this->assertRegExp('/global_cookies/is', $client->getResponse()->getContent());
		$this->assertRegExp('/yii_cookies/is', $client->getResponse()->getContent());

		$client->request('GET', '/test/index');
		$client->request('GET', '/test/getCookies');
		$this->assertRegExp('/global_cookies/is', $client->getResponse()->getContent());
		$this->assertRegExp('/yii_cookies/is', $client->getResponse()->getContent());
	}

	public function testSession()
	{
		$client = static::$wunit->createClient();
		$client->request('GET', '/test/setSession');
		$client->request('GET', '/test/getSession');
		$this->assertRegExp('/global_session/is', $client->getResponse()->getContent());
		$this->assertRegExp('/yii_session/is', $client->getResponse()->getContent());
	}
}
