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
namespace Test\Appfuel\View;

use Test\AfTestCase as ParentTestCase,
	Appfuel\View\ViewFile,
	StdClass;

/**
 * The view file is an application file that knows where the resource
 * directory is.
 */
class ClientsideFileFileTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var ClientsideFile
	 */
	protected $file = null;

	/**
	 * Relative path used in constructor
	 * @var string
	 */
	protected $relativePath = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->relativePath = 'somepath';
		$this->file = new ViewFile($this->relativePath);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->file);
	}

	/**
	 * Test that the default value for the root dir is 'ui' for user interface
	 * and that it can be overwritten
	 *
	 * @return null
	 */
	public function testGetSetRootDir()
	{
		$this->assertEquals('ui', $this->file->getRootDirName());
		$name = 'someOtherDir';
		$this->assertSame(
			$this->file,
			$this->file->setRootDirName($name),
			'must use a fluent interface'
		);
		$this->assertEquals($name, $this->file->getRootDirName());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 */
	public function testEmptyRootDirName()
	{
		$this->file->setRootDirName('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 */
	public function testArrayRootDirName()
	{
		$this->file->setRootDirName(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 */
	public function testIntRootDirName()
	{
		$this->file->setRootDirName(123);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 */
	public function testObjectRootDirName()
	{
		$this->file->setRootDirName(new StdClass());
	}

	/**
	 * Test that the default namespace is the root namespace of the file
	 * class itself and that it also can be overwritten
	 *
	 * @return null
	 */
	public function testGetSetNamespace()
	{
		$class = get_class($this->file);
		$pos = strpos($class, '\\');
		$namespace = strtolower(substr($class, 0, $pos));

		$this->assertEquals($namespace, $this->file->getNamespace());

		$namespace = 'someOtherNamespace';
		$this->assertSame(
			$this->file,
			$this->file->setNamespace($namespace),
			'must be a fluent interface'
		);
		$this->assertEquals($namespace, $this->file->getNamespace());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 */
	public function testEmptyNamespace()
	{
		$this->file->setNamespace('');
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 */
	public function testArrayNamespace()
	{
		$this->file->setNamespace(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 */
	public function testIntNamespace()
	{
		$this->file->setNamespace(123);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 */
	public function testObjectNamespace()
	{
		$this->file->setNamespace(new StdClass());
	}


	/**
	 * Test the file is an SplFileInfo, Appfuel\App\File and getResource
	 * returns the relative path from the resource directory.
	 *
	 * @return null
	 */
	public function testConstructorGetViewPathDefault()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\File\FrameworkFileInterface',
			$this->file
		);

		/* test returning relative */
		$expected = "ui/appfuel/{$this->relativePath}";
		$this->assertEquals($expected, $this->file->getViewPath());
	
		/* test returning absolute */
		$expected = "{$this->file->getBasePath()}/{$expected}";	
		$this->assertEquals($expected, $this->file->getViewPath(true));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 */
	public function testEmptyPathConstructor()
	{
		$file = new ViewFile('');
	}
}
