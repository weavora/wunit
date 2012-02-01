<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class RedirectTest extends \PHPUnit_Framework_TestCase
{

	protected static $wunit;

	public static function setUpBeforeClass()
	{
		static::$wunit = new WUnit();
		static::$wunit->init();
	}

	public function testRedirect()
	{
		$client = static::$wunit->createClient();
		$client->followRedirects(false);
		$client->request('GET', '/test/redirect');

		// or simply check that the response is a redirect to any URL
		$this->assertTrue($client->getResponse()->isRedirect());

		// Assert that the response is a redirect to /site/contact
		$this->assertTrue($client->getResponse()->isRedirect(Yii::app()->createAbsoluteUrl('/test/index')));

		$client->request('GET', '/test/index');
		$this->assertFalse($client->getResponse()->isRedirect());

		$client->request('GET', '/test/redirect');
		$this->assertTrue($client->getResponse()->isRedirect());

		$client->request('GET', '/test/index');
		$this->assertFalse($client->getResponse()->isRedirect());
	}

	public function testFollow()
	{
		$client = static::$wunit->createClient();

		$client->request('GET', '/test/index');
		$this->assertRegExp('/Congratulations\!/is', $client->getResponse()->getContent());

		// by default followRedirects should be enabled
		$client->request('GET', '/test/redirect');
		$this->assertNotEmpty($client->getResponse()->getContent());

		$client->followRedirects(false);
		$client->request('GET', '/test/redirect');
		$this->assertTrue($client->getResponse()->isRedirect());
		$this->assertEmpty($client->getResponse()->getContent());

		$client->followRedirect();
		$this->assertNotEmpty($client->getResponse()->getContent());

		$client->followRedirects(true);
		$client->request('GET', '/test/redirect');
		$this->assertNotEmpty($client->getResponse()->getContent());
	}
}
