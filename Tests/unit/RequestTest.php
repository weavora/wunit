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

	public function testInternals()
	{
		$client = static::$wunit->createClient();
		$client->request('GET', '/test/index');

		$this->assertNotEmpty($client->getHistory());
		$this->assertNotEmpty($client->getCookieJar());
		$this->assertNotEmpty($client->getRequest());
		$this->assertNotEmpty($client->getResponse());
		$this->assertNotEmpty($client->getCrawler());
	}

	public function testUrlManagerFormat()
	{
		$client = static::$wunit->createClient();

		Yii::app()->urlManager->urlFormat = CUrlManager::PATH_FORMAT;

		$crawler = $client->request('GET', '/test/index');
		$this->assertNotEmpty($client->getResponse()->getContent());

		$crawler = $client->request('GET', 'http://localhost/test/index');
		$this->assertNotEmpty($client->getResponse()->getContent());

		Yii::app()->urlManager->urlFormat = CUrlManager::GET_FORMAT;

		$crawler = $client->request('GET', '/index.php', array(Yii::app()->urlManager->routeVar => '/test/index'));
		$this->assertNotEmpty($client->getResponse()->getContent());

		$crawler = $client->request('GET', 'http://localhost/index.php', array(Yii::app()->urlManager->routeVar => '/test/index'));
		$this->assertNotEmpty($client->getResponse()->getContent());

		Yii::app()->urlManager->urlFormat = CUrlManager::PATH_FORMAT;
	}

	public function testResponse()
	{
		$client = static::$wunit->createClient();
		$client->request('GET', '/test/index');

		$this->assertRegExp('/Congratulations\!/is', $client->getResponse()->getContent());

		// Assert the the "Content-Type" header is "text/html"
		$this->assertTrue($client->getResponse()->headers->contains('content-type', 'text/html'));

		// Assert that the response status code is 2xx
		$this->assertTrue($client->getResponse()->isSuccessful());

		// Assert that the response status code is 404
		$this->assertFalse($client->getResponse()->isNotFound());

		// Assert a specific 200 status code
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
	}

	/**
	 * Test multiple clients into separate threads
	 */
	public function testInsulate()
	{
		$this->markTestIncomplete();
//		$client = static::$wunit->createClient();
//		$client->insulate();
//		$client->request('GET', '/test/index');
	}

	public function textHeaders()
	{
		$this->markTestIncomplete();
		$client = static::$wunit->createClient();
		$client->request('GET', '/test/index', array(), array(), array(
			'HTTP_HOST'       => 'en.example.com',
			'HTTP_USER_AGENT' => 'MySuperBrowser/1.0',
		));

		$client = static::$wunit->createClient(array(), array(
			'HTTP_HOST'       => 'en.example.com',
			'HTTP_USER_AGENT' => 'MySuperBrowser/1.0',
		));
		$client->request('GET', '/test/index');
	}


}
