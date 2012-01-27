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

	public function testFormPage()
	{
		$client = static::$wunit->createClient();
		$crawler = $client->request('GET', '/test/form');

		$this->assertTrue($crawler->filter('html:contains("Test Form Functionality")')->count() > 0);
	}

	public function testSubmit(){
		$client = static::$wunit->createClient();
		$crawler = $client->request('GET', '/test/form');
		
		$form = $crawler->selectButton('submit')->form();
		
		$form['FullForm[textField]'] = 'TextFiledValue';
		$form['FullForm[checkBox]']->tick();
		$form['FullForm[dropDownList]']->setvalue('Select first item');
		$form['FullForm[passwordField]'] = 'Password';
		$form['FullForm[textArea]'] = "TextArea";


		$path = dirname(__FILE__).'/../fixtures/files/';
		$filePath = $path.'file.txt';
		$form['FullForm[fileField]']->upload($filePath);		
		
		$crawler = $client->submit($form);

		$this->assertTrue($crawler->filter('html:contains("TextFiledValue")')->count() > 0);
		$this->assertTrue($crawler->filter('html:contains("CheckBox is checked")')->count() > 0);
		$this->assertTrue($crawler->filter('html:contains("Select first item")')->count() > 0);
		$this->assertTrue($crawler->filter('html:contains("Password")')->count() > 0);
		$this->assertTrue($crawler->filter('html:contains("TextArea")')->count() > 0);
		$this->assertTrue($crawler->filter('html:contains("file.txt")')->count() > 0);
	}

	public function testReqValidator(){
		$client = static::$wunit->createClient();
		$crawler = $client->request('GET', '/test/form');

		$form = $crawler->selectButton('submit')->form();
		$form['FullForm[textField]'] = '';

		$crawler = $client->submit($form);
		$this->assertTrue($crawler->filter('html:contains("textField are not empty")')->count() > 0);
	}
}
