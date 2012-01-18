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
		$crawler = $client->request('GET', '/test/redirect');

		# or simply check that the response is a redirect to any URL
		$this->assertTrue($client->getResponse()->isRedirect());

		# Assert that the response is a redirect to /site/contact
		$this->assertTrue($client->getResponse()->isRedirect(Yii::app()->createAbsoluteUrl('/test/index')));
	}
}
