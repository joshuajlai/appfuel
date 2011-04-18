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
namespace Test\Appfuel\Framework;

use Test\AfTestCase	as ParentTestCase,
	Appfuel\App\Factory;

/**
 * Test this class's static methods are able to create the correct objects
 */
class FactoryTest extends ParentTestCase
{
	/**
	 * The request uri string in the $_SERVER super global. 
	 * @var string
	 */
	protected $bkRequestUri = null;

	public function setUp()
	{
		$this->backupServerRequestUri();	
	}

	public function tearDown()
	{
		$this->restoreServerRequestUri();
	}

	/**
	 * This is a unit test run from the command line so this particular
	 * super global key sould not exist, however we will not make that 
	 * assumtion and backup up anyways
	 *
	 * @return null
	 */
	public function backupServerRequestUri()
	{
		$backup = null;
		if (array_key_exists('REQUEST_URI', $_SERVER)) {
			$backup = $_SERVER['REQUEST_URI'];
		}

		$this->bkRequestUri = $backup;
	}

	/**
	 * @return string
	 */
	public function getBackedUpRequestUri()
	{
		return $this->bkRequestUri;
	}

	/**
	 * Restore the server super global key if it existed or unset it if it
	 * did not
	 *
	 * @return null
	 */
	public function restoreServerRequestUri()
	{
		$backup = $this->getBackedUpRequestUri();

			/* restore server super global a*/
		if ($backup === null) {
			unset($_SERVER['REQUEST_URI']);
		} else {
			$_SERVER['REQUEST_URI'] = $backup;
		}
	}

	/**
	 * @return null
	 */
	public function testCreateErrorDisplay()
	{
		$this->assertInstanceOf(
			'\Appfuel\Framework\Env\ErrorDisplay',
			Factory::createErrorDisplay()
		);
	}

	/**
	 * @return null
	 */
	public function testCreateErrorReporting()
	{
		$this->assertInstanceOf(
			'\Appfuel\Framework\Env\ErrorReporting',
			Factory::createErrorReporting()
		);
	}

	/**
	 * @return null
	 */
	public function testCreateAutoloader()
	{
		$this->assertInstanceOf(
			'\Appfuel\Framework\Env\AutoloadInterface',
			Factory::createAutoloader()
		);
	}

	/**
	 * @return null
	 */
	public function testCreateIncludePath()
	{
		$this->assertInstanceOf(
			'\Appfuel\Framework\Env\IncludePath',
			Factory::createIncludePath()
		);
	}

	/**
	 * @return null
	 */
	public function testcreatetimezone()
	{
		$this->assertinstanceof(
			'\appfuel\framework\env\timezone',
			factory::createtimezone()
		);
	}

	/**
	 * Uri string comes from the server super global 'REQUEST_URI'
	 *
	 * @return null
	 */
	public function testcreateUriString()
	{
		$routeString = 'some/fake/route';
		$_SERVER['REQUEST_URI'] = $routeString;

		$uriString = Factory::createUriString();
		$this->assertInternalType('string', $uriString);
		$this->assertEquals($routeString, $uriString);
	}

	/**
	 * The key REQUEST_URI must exist in the server super global
	 *
	 * @expectedException	\Appfuel\Framework\Exception
	 */
	public function testcreateUriStringNoRequestUri()
	{
		unset($_SERVER['REQUEST_URI']);
		$uriString = Factory::createUriString();
	}

	/**
	 * The request uri in the server super global can not be empty
	 *
	 * @expectedException	\Appfuel\Framework\Exception
	 */
	public function testcreateUriStringEmptyStringRequestUri()
	{
		$_SERVER['REQUEST_URI'] = '';
		$uriString = Factory::createUriString();
	}

	/**
	 * @return null
	 */
	public function testCreateUri()
	{
		$routeString = 'some/fake/route';
		$uri = Factory::createUri($routeString);
		$this->assertInstanceOf(
			'Appfuel\Framework\UriInterface',
			$uri
		);

		$this->assertInstanceOf(
			'Appfuel\Framework\Uri',
			$uri
		);
	}

	/**
	 * @return null
	 */
	public function testCreateRequest()
	{
		$routeString = 'some/fake/route';
		$uri     = Factory::createUri($routeString);
		$request = Factory::createRequest($uri, array());

		$this->assertInstanceOf(
			'Appfuel\Framework\RequestInterface',
			$request
		);

		$this->assertInstanceOf(
			'Appfuel\Framework\Request',
			$request
		);
	}


}

