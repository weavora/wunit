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

		$client->request('GET', '/test/index');
		$this->assertNotEmpty($client->getResponse()->getContent());

		$client->request('GET', 'http://localhost/test/index');
		$this->assertNotEmpty($client->getResponse()->getContent());

		Yii::app()->urlManager->urlFormat = CUrlManager::GET_FORMAT;

		$client->request('GET', '/index.php', array(Yii::app()->urlManager->routeVar => '/test/index'));
		$this->assertNotEmpty($client->getResponse()->getContent());

		$client->request('GET', 'http://localhost/index.php', array(Yii::app()->urlManager->routeVar => '/test/index'));
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
		$client = static::$wunit->createClient();

		$client->insulate();
		$client->request('GET', '/test/index');
		$this->assertRegExp('/Congratulations\!/is', $client->getResponse()->getContent());

		$client->request('GET', '/test/global');
		$this->assertEmpty($GLOBALS['global_var']);

		$client->insulate(false);
		$client->request('GET', '/test/global');
		$this->assertNotEmpty($GLOBALS['global_var']);
	}

	public function testServer()
	{
		$client = static::$wunit->createClient();

		$crawler = $client->request('GET', '/test/server');
		$this->assertFalse($crawler->filter('b:contains("HTTP_X_REQUESTED_WITH=XMLHttpRequest")')->count() > 0);
		$this->assertFalse($crawler->filter('b:contains("HTTP_USER_AGENT=MySuperBrowser/1.0")')->count() > 0);

		$crawler = $client->request('GET', '/test/server', array(), array(), array(
			'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
			'HTTP_USER_AGENT' => 'MySuperBrowser/1.0',
		));

		$this->assertTrue($crawler->filter('b:contains("HTTP_X_REQUESTED_WITH=XMLHttpRequest")')->count() > 0);
		$this->assertTrue($crawler->filter('b:contains("HTTP_USER_AGENT=MySuperBrowser/1.0")')->count() > 0);

		$client = static::$wunit->createClient(array(), array(
			'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
			'HTTP_USER_AGENT' => 'MySuperBrowser/1.0',
		));

		$client->request('GET', '/test/server');

		$this->assertTrue($crawler->filter('b:contains("HTTP_X_REQUESTED_WITH=XMLHttpRequest")')->count() > 0);
		$this->assertTrue($crawler->filter('b:contains("HTTP_USER_AGENT=MySuperBrowser/1.0")')->count() > 0);

		$client->request('GET', '/test/server');

		$this->assertTrue($crawler->filter('b:contains("HTTP_X_REQUESTED_WITH=XMLHttpRequest")')->count() > 0);
		$this->assertTrue($crawler->filter('b:contains("HTTP_USER_AGENT=MySuperBrowser/1.0")')->count() > 0);
	}

	public function testYiiEnd()
	{
		$client = static::$wunit->createClient();
		$client->request('GET', '/test/yiiEnd');
		$this->assertRegExp('/before/is', $client->getResponse()->getContent());
		$this->assertNotRegExp('/after/is', $client->getResponse()->getContent());
	}

	public function testRegisterShutdown()
	{
		$client = static::$wunit->createClient();
		$client->request('GET', '/test/registerShutdown');
		$this->assertNotRegExp('/shutdown/is', $client->getResponse()->getContent());
	}
}
