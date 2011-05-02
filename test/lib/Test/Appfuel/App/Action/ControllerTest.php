<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Test\Appfuel\App\Action;

use Test\AfTestCase	as ParentTestCase,
	Appfuel\App\Action\Controller,
	StdClass;

/**
 * The request object was designed to service web,api and cli request
 */
class ControllerTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Request
	 */
	protected $controller = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->controller = new Controller();
	}

	/**
	 * Restore the super global data
	 * 
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->controller);
	}

	/**
	 * The front controller will ask to action controller if it supports the
	 * requested reponse type with isSupportedDoc method. There also exists
	 * addSupportedDoc, addSupportedDocs and getSupportedDocs to allow 
	 * developers to manage a list of supported documents
	 *
	 * @return null
	 */
	public function testAddGetIsSupportedDoc()
	{	
		/* the controller assumes no docs are supported you must give
		 * your controller that information either during instaniation or
		 * initialization
		 */
		$result = $this->controller->getSupportedDocs();
		$this->assertInternalType('array', $result);
		$this->assertEmpty($result);
		
		$this->assertSame(
			$this->controller, 
			$this->controller->addSupportedDoc('html'),
			'uses a fluent interface'
		);

		$result = $this->controller->getSupportedDocs();
		$this->assertInternalType('array', $result);
		$this->assertContains('html', $result);
		$this->assertTrue($this->controller->isSupportedDoc('html'));

		/* add a second type to the list */
		$this->controller->addSupportedDoc('json');
		$result = $this->controller->getSupportedDocs();
		$this->assertInternalType('array', $result);
		$this->assertTrue($this->controller->isSupportedDoc('html'));
		$this->assertContains('html', $result);

		$this->assertTrue($this->controller->isSupportedDoc('json'));
		$this->assertContains('json', $result);

		/* test for a type that does not exist */
		$this->assertFalse($this->controller->isSupportedDoc('none-found'));	
	}

	/**
	 * Add a list of supported docs all at the same time
	 *
	 * @return null
	 */
	public function testAddSupportedDocs()
	{
		$types = array(
			'json',
			'html',
			'xml',
			'bin'
		);

		$this->assertSame(
			$this->controller,
			$this->controller->addSupportedDocs($types),
			'uses a fluent interface'
		);

		$this->assertEquals($types, $this->controller->getSupportedDocs());
		
		foreach ($types as $type) {
			$this->assertTrue($this->controller->isSupportedDoc($type));
		}
	}

	/**
	 * Test the invalid cases for adding a single type
	 *
	 * @return null
	 */
	public function testAddSupportedDocInvalidType()
	{
		$result = $this->controller->getSupportedDocs();
		$this->assertInternalType('array', $result);
		$this->assertEmpty($result);

		$this->assertSame(
			$this->controller,
			$this->controller->addSupportedDoc(array(1,2,3)),
			'will not throw an error even though its not valid'
		);
		$result = $this->controller->getSupportedDocs();
		$this->assertInternalType('array', $result);
		$this->assertEmpty($result);
		$this->assertFalse($this->controller->isSupportedDoc(array(1,2,3)));
	
		$this->assertSame(
			$this->controller,
			$this->controller->addSupportedDoc(123),
			'will not throw an error even though its not valid'
		);
		$result = $this->controller->getSupportedDocs();
		$this->assertInternalType('array', $result);
		$this->assertEmpty($result);
		$this->assertFalse($this->controller->isSupportedDoc(123));

		$type = new StdClass();
		$this->assertSame(
			$this->controller,
			$this->controller->addSupportedDoc($type),
			'will not throw an error even though its not valid'
		);
		$result = $this->controller->getSupportedDocs();
		$this->assertInternalType('array', $result);
		$this->assertEmpty($result);
		$this->assertFalse($this->controller->isSupportedDoc($type));
	}

	/**
	 * @return null
	 */
	public function testAddSupportedDocsInvalidTypes()
	{
		$types = array(
			'',
			1234,
			array(1,2,3,4,5),
			new StdClass()
		);

		$this->assertSame(
			$this->controller,
			$this->controller->addSupportedDocs($types),
			'will not throw any errors'
		);

		$result = $this->controller->getSupportedDocs();
		$this->assertInternalType('array', $result);
		$this->assertEmpty($result);
	}

	/**
	 * @return null
	 */
	public function testGetSetViewManager()
	{
		$view = $this->getMock('\Appfuel\Framework\View\ManagerInterface');
		
		/* initial value is null */
		$this->assertNull($this->controller->getViewManager());

		$this->assertSame(
			$this->controller,
			$this->controller->setViewManager($view),
			'uses fluent  interface'
		);

		$this->assertSame($view, $this->controller->getViewManager());
	}
}

