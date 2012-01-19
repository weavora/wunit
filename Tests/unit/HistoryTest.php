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

	public function testOne()
	{
		$this->markTestIncomplete();
		$client = static::$wunit->createClient();
		$crawler = $client->request('GET', '/test/index');

		$client->back();
		$client->forward();
		$client->reload();

		# Clears all cookies and the history
		$client->restart();
	}

	public function testCookies()
	{

	}

	public function testSession()
	{

	}
}
