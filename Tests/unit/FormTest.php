<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class FormTest extends \PHPUnit_Framework_TestCase
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
	}
}
