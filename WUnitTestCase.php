<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

abstract class WUnitTestCase extends CDbTestCase {

	public function setUp() {
		$this->useErrorHandler = true;
//		$this->setExpectedException('Exception');
		parent::setUp();

		set_exception_handler(array($this, 'excHandler'));
	}

	public function excHandler($exception) {
		 echo "Uncaught exception: " , $exception->getMessage(), "\n";
	}

	/**
	 * Create HttpKernel Client
	 * @return WUnit\HttpKernel\Client
	 */
	public static function createClient() {
		return Yii::app()->wunit->createClient();
	}
}
