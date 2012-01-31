WUnit
=======

Functional testing without Selenium
-----------------------------------

Would you like to create functional tests in [symfony2 style](http://symfony.com/doc/2.0/book/testing.html) without Selenium?
What could be easier! Just install ``wunit`` extension.

Note that ``wunit`` based on some ``symfony2`` classes and support all ``symfony2`` testing features
(of course, except for directly related to ``symfony2`` core like profiling)

Requirements
-----------

* PHP 5.3.x or higher
* PHPUnit 3.6.x or higher
* XDebug extension installed

Installation
------------

1) Download and unpack source into protected/extensions/wunit folder.

2) Import wunit into test config (protected/config/**test.php**):

```ruby
# protected/config/test.php
return array(
    ...
    'import' => array(
        ...
        'ext.wunit.*',
    ),
	...
	'components' => array(
		...
		'wunit' => array(
			'class' => 'WUnit'
		),
    ...
);
```

3) Update protected/tests/bootstrap.php

Replace line
```
Yii::createWebApplication($config);
```
with

```
require(dirname(__FILE__) . '/../extensions/wunit/WUnit.php');
WUnit::createWebApplication($config);
```

Finally you should get something like:

```ruby
$yiit=dirname(__FILE__).'/../../../framework/yiit.php';
$config=dirname(__FILE__).'/../config/test.php';

require_once($yiit);

require(dirname(__FILE__) . '/../extensions/wunit/WUnit.php');
WUnit::createWebApplication($config);
```

4) Replace protected/tests/phpunit.xml with:

```xml
<phpunit
bootstrap="bootstrap.php"
convertErrorsToExceptions="true"
convertNoticesToExceptions="true"
convertWarningsToExceptions="true"
printerClass="WUnit_ResultPrinter"
printerFile="../extensions/wunit/PHPUnit/ResultPrinter.php"
stopOnFailure="false"
/>
```

**NOTICE** that ``printerClass`` and ``printerFile`` options are very important.

5*) To test file uploading you should use UploadedFile class instead of CUploadedFile. Here is example:

```ruby
# protected/config/main.php
return array(
	...
    'import' => array(
    	...
        'ext.wunit.*',
    ),
);

# protected/controllers/TestController.php
public function actionFormWithFile() {
	$form = new SomeForm();
	if (Yii::app()->request->getParam('FullForm')) {
		$form->attributes = Yii::app()->request->getParam('FullForm');
		$form->fileField = UploadedFile::getInstanceByName("FullForm[fileField]");
		if ($form->validate()) {
			$form->fileField->saveAs(dirname(__FILE__).'/../files/tmp.txt');
		}
	}
}
```

That's it. Now you can use create proper functional tests without Selenium :)


Your First Functional Test
--------------------------

Functional tests are simple PHP files that typically live in the ``protected/tests/functional/`` folder.
If you would like to test the pages handled by your ``SiteController`` class, start by creating
a new ``SiteControllerTest.php`` file that extends a special ``WUnitTestCase`` class.

```ruby
class SiteControllerTest extends WUnitTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/site/index');

        $this->assertTrue($crawler->filter('html:contains("Congratulations!")')->count() > 0);
    }
}
```

The ``createClient()`` method returns a client, which is like a browser that you'll use to crawl your site:

```ruby
$crawler = $client->request('GET', '/site/index');
```

The ``request()`` method returns a ``Crawler`` object which can be used to select elements in the Response, click on links, and submit forms.

The Crawler only works when the response is an XML or an HTML document.
To get the raw content response, call $client->getResponse()->getContent().

Click on a link by first selecting it with the Crawler using either an XPath
expression or a CSS selector, then use the Client to click on it. For example,
the following code finds all links with the text ``Great``, then selects
the second one, and ultimately clicks on it:

```ruby
$link = $crawler->filter('a:contains("Great")')->eq(0)->link();

$crawler = $client->click($link);
```

Submitting a form is quite similar; select a form button, optionally override
some form values, and submit the corresponding form:

```ruby
$form = $crawler->selectButton('submit')->form();

# set some values
$form['name'] = 'Lucas';
$form['form_name[subject]'] = 'Hey there!';

# submit the form
$crawler = $client->submit($form);
```

The form can also handle uploads and contains methods to fill in different types
of form fields (e.g. select() and tick()). For details, see the
`Forms` section below.

Now when you can easily navigate through an application, use assertions to test
that it actually does what you expect it to do. Use the ``Crawler`` to make assertions
on the DOM:

```ruby
# Assert that the response matches a given CSS selector.
$this->assertTrue($crawler->filter('h1')->count() > 0);
```

Or, test against the Response content directly if you just want to assert that
the content contains some text, or if the Response is not an XML/HTML
document:

```ruby
$this->assertRegExp('/Hello Chris/', $client->getResponse()->getContent());
```
# Run Tests
From protected/tests:

```ruby
phpunit unit //run all tests from unit folder
phpunit functional //run all tests from functional folder
phpunit functional/SiteControllerTest.php // run specific test
```

**NOTICE** WUnit do not require selenium, and if it is not installed on your environment
then just comment out the following line in file protected/tests/bootstrap.php

```ruby
#protected/tests/bootstrap.php
require_once(dirname(__FILE__).'/WebTestCase.php');
```

# More about request() method

The full signature of the ``request()`` method is:

    request(
        $method,
        $uri,
        array $parameters = array(),
        array $files = array(),
        array $server = array(),
        $content = null,
        $changeHistory = true
    )

The ``server`` array is the raw values that you'd expect to normally
find in the PHP `$_SERVER`_ superglobal. For example, to set the `Content-Type`
and `Referer` HTTP headers, you'd pass the following:

```ruby
$client->request(
    'GET',
    '/site/page/about',
    array(),
    array(),
    array(
        'CONTENT_TYPE' => 'application/json',
        'HTTP_REFERER' => '/foo/bar',
    )
);
```


### Useful Assertions

To get you started faster, here is a list of the most common and
useful test assertions:

```ruby
# Assert that there is exactly one h2 tag with the class "subtitle"
$this->assertTrue($crawler->filter('h2.subtitle')->count() > 0);

# Assert that there are 4 h2 tags on the page
$this->assertEquals(4, $crawler->filter('h2')->count());

# Assert the the "Content-Type" header is "application/json"
$this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

# Assert that the response content matches a regexp.
$this->assertRegExp('/foo/', $client->getResponse()->getContent());

# Assert that the response status code is 2xx
$this->assertTrue($client->getResponse()->isSuccessful());
# Assert that the response status code is 404
$this->assertTrue($client->getResponse()->isNotFound());
# Assert a specific 200 status code
$this->assertEquals(200, $client->getResponse()->getStatusCode());

# Assert that the response is a redirect to /site/contact
$this->assertTrue($client->getResponse()->isRedirect(Yii::app()->createAbsoluteUrl('/site/contact')));
# or simply check that the response is a redirect to any URL
$this->assertTrue($client->getResponse()->isRedirect());
```

Working with the Test Client
-----------------------------

The test Client simulates an HTTP client like a browser and makes requests
to your Yii application:

```ruby
$crawler = $client->request('GET', '/site/index');
# or
$crawler = $client->request('GET', '/');
```

The ``request()`` method takes the HTTP method and a URL as arguments and
returns a ``Crawler`` instance.

Use the Crawler to find DOM elements in the Response. These elements can then
be used to click on links and submit forms:

```ruby
$link = $crawler->selectLink('Go elsewhere...')->link();
$crawler = $client->click($link);

$form = $crawler->selectButton('validate')->form();
$crawler = $client->submit($form, array('name' => 'Chris'));
```

The ``click()`` and ``submit()`` methods both return a ``Crawler`` object.
These methods are the best way to browse your application as it takes care
of a lot of things for you, like detecting the HTTP method from a form and
giving you a nice API for uploading files.


The ``request`` method can also be used to simulate form submissions directly
or perform more complex requests:

```ruby
# Directly submit a form (but using the Crawler is easier!)
$client->request('POST', '/submit', array('name' => 'Chris'));

# Form submission with a file upload
use Symfony\HttpFoundation\File\UploadedFile;

$photo = new UploadedFile(
    '/path/to/photo.jpg',
    'photo.jpg',
    'image/jpeg',
    123
);
# or
$photo = array(
    'tmp_name' => '/path/to/photo.jpg',
    'name' => 'photo.jpg',
    'type' => 'image/jpeg',
    'size' => 123,
    'error' => UPLOAD_ERR_OK
);
$client->request(
    'POST',
    '/submit',
    array('name' => 'Chris'),
    array('photo' => $photo)
);

# Perform a DELETE requests, and pass HTTP headers
$client->request(
    'DELETE',
    '/post/12',
    array(),
    array(),
    array('PHP_AUTH_USER' => 'username', 'PHP_AUTH_PW' => 'pa$$word')
);
```

Last but not least, you can force each request to be executed in its own PHP
process to avoid any side-effects when working with several clients in the same
script:

```ruby
$client->insulate();
```

### Browsing

The Client supports many operations that can be done in a real browser:

```ruby
$client->back();
$client->forward();
$client->reload();

# Clears all cookies and the history
$client->restart();
```

### Accessing Internal Objects

If you use the client to test your application, you might want to access the
client's internal objects:

```ruby
$history   = $client->getHistory();
$cookieJar = $client->getCookieJar();
```

You can also get the objects related to the latest request:

```ruby
$request  = $client->getRequest();
$response = $client->getResponse();
$crawler  = $client->getCrawler();
```


### Redirecting


When a request returns a redirect response, the client automatically follows
it. If you want to examine the Response before redirecting, you can force
the client to skip following redirects with the  ``followRedirects()`` method:

```ruby
$client->followRedirects(false);
```

When the client does not follow redirects, you can force the redirection with
the ``followRedirect()`` method:

```ruby
$crawler = $client->followRedirect();
```

The Crawler
-----------

A Crawler instance is returned each time you make a request with the Client.
It allows you to traverse HTML documents, select nodes, find links and forms.

### Traversing

Like jQuery, the Crawler has methods to traverse the DOM of an HTML/XML
document. For example, the following finds all ``input[type=submit]`` elements,
selects the last one on the page, and then selects its immediate parent element:

```ruby
$newCrawler = $crawler->filter('input[type=submit]')
    ->last()
    ->parents()
    ->first()
;
```

Many other methods are also available:

<table>
    <tr>
        <th>Method</th>
        <th>Description</th>
    </tr>
    <tr>
        <td>filter('h1.title')</td>
        <td>Nodes that match the CSS selector </td>
    </tr>
    <tr>
        <td>filterXpath('h1')</td>
        <td>Nodes that match the XPath expression  </td>
    </tr>
    <tr>
        <td>eq(1)</td>
        <td>Node for the specified index   </td>
    </tr>
    <tr>
        <td>first()</td>
        <td>First node   </td>
    </tr>
    <tr>
        <td>last()</td>
        <td>Last node</td>
    </tr>
    <tr>
        <td>siblings()</td>
        <td>Siblings </td>
    </tr>
    <tr>
        <td>nextAll()</td>
        <td>All following siblings  </td>
    </tr>
    <tr>
        <td>previousAll()</td>
        <td>All preceding siblings</td>
    </tr>
    <tr>
        <td>parents()</td>
        <td>Parent nodes</td>
    </tr>
    <tr>
        <td>children()</td>
        <td>Children</td>
    </tr>
    <tr>
        <td>reduce($lambda)</td>
        <td>Nodes for which the callable does not return false</td>
    </tr>
</table>

Since each of these methods returns a new ``Crawler`` instance, you can
narrow down your node selection by chaining the method calls:

```ruby
$crawler
    ->filter('h1')
    ->reduce(function ($node, $i)
    {
        if (!$node->getAttribute('class')) {
            return false;
        }
    })
    ->first();
```


Use the ``count()`` function to get the number of nodes stored in a Crawler:
``count($crawler)``

### Extracting Information

The Crawler can extract information from the nodes:

```ruby
# Returns the attribute value for the first node
$crawler->attr('class');

# Returns the node value for the first node
$crawler->text();

# Extracts an array of attributes for all nodes (_text returns the node value)
# returns an array for each element in crawler, each with the value and href
$info = $crawler->extract(array('_text', 'href'));

# Executes a lambda for each node and return an array of results
$data = $crawler->each(function ($node, $i)
{
    return $node->getAttribute('href');
});
```

### Links

To select links, you can use the traversing methods above or the convenient
``selectLink()`` shortcut:

```ruby
$crawler->selectLink('Click here');
```

This selects all links that contain the given text, or clickable images for
which the ``alt`` attribute contains the given text. Like the other filtering
methods, this returns another ``Crawler`` object.

Once you've selected a link, you have access to a special ``Link`` object,
which has helpful methods specific to links (such as ``getMethod()`` and
``getUri()``). To click on the link, use the Client's ``click()`` method
and pass it a ``Link`` object:

```ruby
$link = $crawler->selectLink('Click here')->link();

$client->click($link);
```

### Forms

Just like links, you select forms with the ``selectButton()`` method:

```ruby
$buttonCrawlerNode = $crawler->selectButton('submit');
```

Notice that we select form buttons and not forms as a form can have several
buttons; if you use the traversing API, keep in mind that you must look for a
button.

The ``selectButton()`` method can select ``button`` tags and submit ``input``
tags. It uses several different parts of the buttons to find them:

* The ``value`` attribute value;

* The ``id`` or ``alt`` attribute value for images;

* The ``id`` or ``name`` attribute value for ``button`` tags.

Once you have a Crawler representing a button, call the ``form()`` method
to get a ``Form`` instance for the form wrapping the button node:

```ruby
$form = $buttonCrawlerNode->form();
```

When calling the ``form()`` method, you can also pass an array of field values
that overrides the default ones:

```ruby
$form = $buttonCrawlerNode->form(array(
    'name'              => 'Chris',
    'my_form[subject]'  => 'Weavora rocks!',
));
```

And if you want to simulate a specific HTTP method for the form, pass it as a
second argument:

```ruby
$form = $crawler->form(array(), 'DELETE');
```

The Client can submit ``Form`` instances:

```ruby
$client->submit($form);
```

The field values can also be passed as a second argument of the ``submit()``
method:

```ruby
$client->submit($form, array(
    'name'              => 'Chris',
    'my_form[subject]'  => 'Weavora rocks!',
));
```

For more complex situations, use the ``Form`` instance as an array to set the
value of each field individually:

```ruby
# Change the value of a field
$form['name'] = 'Chris';
$form['my_form[subject]'] = 'Weavora rocks!';
```

There is also a nice API to manipulate the values of the fields according to
their type:

```ruby
# Select an option or a radio
$form['country']->select('France');

# Tick a checkbox
$form['like_weavora']->tick();

# Upload a file
$form['photo']->upload('/path/to/lucas.jpg');
```

You can get the values that will be submitted by calling the ``getValues()``
method on the ``Form`` object. The uploaded files are available in a
separate array returned by ``getFiles()``. The ``getPhpValues()`` and
``getPhpFiles()`` methods also return the submitted values, but in the
PHP format (it converts the keys with square brackets notation - e.g.
``my_form[subject]`` - to PHP arrays).

HTTP headers
---------------------

If your application behaves according to some HTTP headers, pass them as the
second argument of ``createClient()``:

```ruby
$client = static::createClient(array(), array(
    'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
    'HTTP_USER_AGENT'       => 'MySuperBrowser/1.0',
));
```

You can also override HTTP headers on a per request basis:

```ruby
$client->request('GET', '/', array(), array(), array(
    'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest',
    'HTTP_USER_AGENT'       => 'MySuperBrowser/1.0',
));
```