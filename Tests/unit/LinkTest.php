<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class LinkTest extends \PHPUnit_Framework_TestCase
{
	protected static $wunit;

	public static function setUpBeforeClass()
	{
		static::$wunit = new WUnit();
		static::$wunit->init();
	}

	public function testFollow()
	{

		$client = static::$wunit->createClient();
		$crawler = $client->request('GET', '/test/index');

		$link = $crawler->selectLink('Contact')->link();
		$this->assertNotEmpty($link);

		$crawler = $client->click($link);
		$this->assertTrue($crawler->filter('html:contains("Contact Us")')->count() > 0);

	}
}
