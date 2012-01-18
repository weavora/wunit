<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

class CrawlerTest extends \PHPUnit_Framework_TestCase
{

	protected static $wunit;

	public static function setUpBeforeClass()
	{
		static::$wunit = new WUnit();
		static::$wunit->init();
	}

	public function testFilters()
	{
		$client = static::$wunit->createClient();
		$crawler = $client->request('GET', '/test/index');

		$this->assertTrue($crawler->filter('html:contains("Congratulations!")')->count() > 0);
		$this->assertTrue($crawler->filter('a:contains("Home")')->count() > 0);
		$this->assertNotEmpty($crawler->filter('a:contains("Home")')->eq(1));
		$this->assertTrue($crawler->filter('h1')->count() > 0);
		// Assert that there is exactly one h2 tag with the class "subtitle"
		$this->assertTrue($crawler->filter('h2.cssclass')->count() > 0);

		// Assert that there are 4 h2 tags on the page
		$this->assertEquals(1, $crawler->filter('h2')->count());

	}

	public function testTraversing()
	{
		$client = static::$wunit->createClient();
		$crawler = $client->request('GET', '/test/index');

		$h2WithClass = $crawler
			->filter('h2')
			->reduce(function ($node, $i)
			{
				if (!$node->getAttribute('class')) {
					return false;
				}
			})
			->first();

		$this->assertNotEmpty($h2WithClass);
	}

	public function testExtracting()
	{
		$client = static::$wunit->createClient();
		$crawler = $client->request('GET', '/test/index');

		# Returns the attribute value for the first node
		$this->assertEquals($crawler->filter('h2')->attr('class'), 'cssclass');

		# Returns the node value for the first node
		$this->assertEquals($crawler->filter('h2')->text(), 'cool');

		# Extracts an array of attributes for all nodes (_text returns the node value)
		# returns an array for each element in crawler, each with the value and href
		$links = $crawler->extract(array('_text', 'href'));
		$this->assertTrue(count($links) > 0);

		# Executes a lambda for each node and return an array of results
		$data = $crawler->each(function ($node, $i)
		{
			return $node->getAttribute('href');
		});

		$this->assertTrue(count($data) > 0);
	}
}
