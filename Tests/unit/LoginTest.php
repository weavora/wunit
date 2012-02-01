<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class LoginTest extends \PHPUnit_Framework_TestCase
{

	protected static $wunit;

	public static function setUpBeforeClass()
	{
		static::$wunit = new WUnit();
		static::$wunit->init();
	}

	public function testLoginPage()
	{
		$client = static::$wunit->createClient();
		$crawler = $client->request('GET', '/test/login');


		$this->assertTrue($crawler->filter('html:contains("Please fill out the following form with your login credentials")')->count() > 0);
	}

	public function testLoginForm()
	{
		$client = static::$wunit->createClient();

		$usermame = "demo";
		$password = "demo";
		
		$crawler = $client->request('GET', '/test/index');
		$this->assertTrue($crawler->filter('html:contains("You are not loged in")')->count() > 0);

		$this->assertTrue($this->login($client, $usermame, $password)->filter('html:contains("You are loged in as demo")')->count() > 0);

		$client->back();
		$crawler = $client->request('GET', '/test/index');
		$this->assertTrue($crawler->filter('html:contains("You are loged in as demo")')->count() > 0);

		$crawler = $client->request('GET', '/test/logout');

		$this->assertTrue($crawler->filter('html:contains("You are not loged in")')->count() > 0);
		$crawler = $client->request('GET', '/test/index');

		$this->assertTrue($this->login($client, 'demo', 'demo')->filter('html:contains("You are loged in as demo")')->count() > 0);
	}

	private function login($client, $username, $password){
		$crawler = $client->request('GET', '/test/login');

		$form = $crawler->selectButton('Login')->form();
		$form['LoginForm[username]'] = $username;
		$form['LoginForm[password]'] = $password;
		$form['LoginForm[rememberMe]']->tick();

		return $client->submit($form);
	}

}