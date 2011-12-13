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
namespace TestFuel\Unit\ClassLoader;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\ClassLoader\StandardAutoLoader;

/**
 * The standard autoloader can both manual parser and load a php namespace
 * into memory or be registered as an autoloader to do the samething. The
 * auto loader has a namespace parse so test its usage. We test the ability
 * register and unregister the autoloader. We test the ability to add search
 * paths and we test that we can load a class
 */
class StandardAutoLoaderTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var StandardAutoLoader
	 */
	protected $loader = null;

	/**	
	 * @return	null
	 */
	public function setUp()
	{
		parent::setUp();
		$this->loader = new StandardAutoLoader();
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		parent::tearDown();
		$this->loader = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\ClassLoader\AutoLoaderInterface',
			$this->loader
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testParser()
	{
		/* when nothing is given a parser is created for you */
		$parser = $this->loader->getParser();
		$this->assertInstanceOf(
			'Appfuel\ClassLoader\NamespaceParser',
			$parser
		);

		$new = $this->getMock('Appfuel\ClassLoader\NamespaceParserInterface');
		$this->assertSame(
			$this->loader,
			$this->loader->setParser($new),
			'uses fluent interface'
		);
		
		$this->assertSame($new, $this->loader->getParser());

		$loader = new StandardAutoLoader(null, $new);
		$this->assertSame($new, $loader->getParser());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testIsIncludePath()
	{
		$this->assertFalse($this->loader->isIncludePathEnabled());
		$this->assertSame(
			$this->loader,
			$this->loader->enableIncludePath(),
			'uses fluent interface'
		);
		$this->assertTrue($this->loader->isIncludePathEnabled());
	
		$this->assertSame(
			$this->loader,
			$this->loader->disableIncludePath(),
			'uses fluent interface'
		);	
		$this->assertFalse($this->loader->isIncludePathEnabled());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testPath()
	{
		$this->assertEquals(array(), $this->loader->getPaths());
		
		$path1 = '/my/path';
		$path2 = '/your/path';
		$path3 = '/our/path';
		$this->assertSame(
			$this->loader,
			$this->loader->addPath($path1)
		);
		$expected = array($path1);
		$this->assertEquals($expected, $this->loader->getPaths());

		$this->assertSame(
			$this->loader,
			$this->loader->addPath($path2)
		);
		$expected = array($path1, $path2);
		$this->assertEquals($expected, $this->loader->getPaths());
	
		$this->assertSame(
			$this->loader,
			$this->loader->addPath($path3)
		);
		$expected = array($path1, $path2, $path3);
		$this->assertEquals($expected, $this->loader->getPaths());

		/* does not add duplicates */
		$this->loader->addPath($path1)
					 ->addPath($path1)
					 ->addPath($path2)
					 ->addPath($path3);

		$this->assertEquals($expected, $this->loader->getPaths());
	}

	/**
	 * The first parameter can be a single path or an array of paths
	 * 
	 * @depends		testPath
	 * @return		null
	 */
	public function testPathConstructorSinglePath()
	{
		$path     = '/my/path';
		$loader   = new StandardAutoLoader($path);
		$expected = array($path);
		$this->assertEquals($expected, $loader->getPaths());
	}

	/**
	 * @depends		testPath
	 * @return		null
	 */
	public function testPathConstructorManyPaths()
	{
		$path     = array('/my/path', 'my/other/path');
		$loader   = new StandardAutoLoader($path);
		$this->assertEquals($path, $loader->getPaths());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends				testPath
	 * @return				null
	 */
	public function testPathConstructor_IntFailure()
	{
		$path     = 12345;
		$loader   = new StandardAutoLoader($path);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends				testPath
	 * @return				null
	 */
	public function testPathConstructor_ArrayFailure()
	{
		$path     = array(2,23,4);
		$loader   = new StandardAutoLoader($path);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @depends				testPath
	 * @return				null
	 */
	public function testPathConstructor_ObjectFailure()
	{
		$path     = new StdClass();
		$loader   = new StandardAutoLoader($path);
	}

	/**
	 * @return	array
	 */
	public function providePathsWithWhitespaces()
	{
		$result = 'my/path';
		return	array(
			array(' my/path ', $result),
			array("\tmy/path", $result),
			array(" \tmy/path", $result),
			array("\nmy/path", $result),
			array("\n my/path \n", $result)
		);
	}

	/**
	 * @dataProvider	providePathsWithWhitespaces
	 * @depends			testPath
	 * @return			null
	 */
	public function testPathWithWhitespaces($input, $expected)
	{
		$this->loader->addPath($input);

		$expected = array($expected);
		$this->assertEquals($expected, $this->loader->getPaths());
	}

	/**
	 * @return	array
	 */
	public function provideInvalidPaths()
	{
		return	array(
			array(''),
			array(' '),
			array("\t"),
			array("\n"),
			array(" \t\n"),
			array(12345),
			array(1.23),
			array(array(1,2,3)),
			array(new StdClass())
		);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @dataProvider		provideInvalidPaths
	 * @depends				testPath
	 * @return				null
	 */
	public function testPath_Failures($input)
	{
		$this->loader->addPath($input);
	}

	/**
	 * @depends	testInterface
	 */
	public function testRegister()
	{
		$this->clearAutoloaders();
		$result = $this->loader->register();
		$list = spl_autoload_functions();
		$this->restoreAutoloaders();	
		
		$expected = array(array($this->loader, 'loadClass'));	
		$this->assertEquals($expected, $list);
	}

	/**
	 * @depends	testRegister
	 * @return	null
	 */
	public function testUnregister()
	{
		$this->clearAutoloaders();
		$this->loader->register();
		$result = $this->loader->unregister();
		$list = spl_autoload_functions();
		$this->restoreAutoloaders();	

		$this->assertEquals(array(), $list);
	}

	/**
	 * LoadClass will return true on files that have been resolved to a path,
	 * located on disk and loaded into memory..
	 * 
	 * Note: whenever we clear the autoloader when can not run any phpunit
	 * classes because its autoloader is cleared. So we save the results,
	 * restore the autoloaders and then test the results.
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testLoadClass()
	{
		$class = 'TestFuel\Fake\ClassLoader\LoadMe';
		$this->loader->addPath(AF_LIB_PATH);
		$this->assertNotContains($class, get_declared_classes());
		
		$this->clearAutoloaders();
		$result = $this->loader->loadClass($class);

		$this->restoreAutoloaders();
		$this->assertTrue($result);
		$this->assertContains($class, get_declared_classes());
	}

	/**
	 * LoadClass will return false on classes or interfaces already loaded
	 * 
	 * Note: whenever we clear the autoloader when can not run any phpunit
	 * classes because its autoloader is cleared. So we save the results,
	 * restore the autoloaders and then test the results.
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testLoadClassesAlreadyLoaded()
	{
		$class     = 'Appfuel\ClassLoader\StandardAutoLoader';
		$interface = 'Appfuel\ClassLoader\AutoLoaderInterface';
		$this->loader->addPath(AF_LIB_PATH);
		$this->assertContains($class, get_declared_classes());
		$this->assertContains($interface, get_declared_interfaces());
		
		$this->clearAutoloaders();
		$cresult = $this->loader->loadClass($class);
		$iresult = $this->loader->loadClass($interface);

		$this->restoreAutoloaders();
		$this->assertFalse($cresult);
		$this->assertFalse($iresult);
	}


	/**
	 * LoadClass will return null when the resolved file for that class can
	 * not be found
	 *
	 * Note: whenever we clear the autoloader when can not run any phpunit
	 * classes because its autoloader is cleared. So we save the results,
	 * restore the autoloaders and then test the results.
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testLoadClassNotFound()
	{
		$class    = 'TestFuel\Fake\ClassLoader\LoadMeNotFound';
		$this->assertNotContains($class, get_declared_classes());
		$this->clearAutoloaders();
	
		$this->loader->addPath(AF_LIB_PATH);
		$this->loader->register();
	
		$result = $this->loader->loadClass($class);
		$this->restoreAutoloaders();
		$this->assertNull($result);
		$this->assertNotContains($class, get_declared_classes());
	}
}
