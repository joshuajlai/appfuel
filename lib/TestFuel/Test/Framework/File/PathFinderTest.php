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
namespace TestFuel\Test\Framework\File;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Framework\File\PathFinder;

/**
 */
class PathFinderTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var PathFinder
	 */
	protected $finder = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->finder = new PathFinder();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->finder = null;
	}

	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\File\PathFinderInterface',
			$this->finder
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testConstructorDefault()
	{
		$this->assertTrue(defined('AF_BASE_PATH'));
		$this->assertEquals(AF_BASE_PATH, $this->finder->getBasePath());
		$this->assertTrue($this->finder->isBasePathEnabled());
		$this->assertEquals('', $this->finder->getRelativeRootPath());
	}

	/**
	 * @depends	testConstructorDefault
	 * @return	null
	 */
	public function testEnableDisableIsBasePathEnabled()
	{
		$this->assertTrue($this->finder->isBasePathEnabled());
		$this->assertSame(
			$this->finder,
			$this->finder->disableBasePath(),
			'uses fluent interface'
		);
		$this->assertFalse($this->finder->isBasePathEnabled());

		$this->assertSame(
			$this->finder,
			$this->finder->enableBasePath(),
			'uses fluent interface'
		);
		$this->assertTrue($this->finder->isBasePathEnabled());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetRelativeRootBasePathEnabled()
	{
		$path = 'my/relative/root';
		$this->assertNotEquals($path, $this->finder->getRelativeRootPath());

		$this->assertSame(
			$this->finder,
			$this->finder->setRelativeRootPath($path),
			'uses a fluent interface'
		);

		$this->assertEquals($path, $this->finder->getRelativeRootPath());

		/* it is valid to set the relative root to an empty string */
		$this->assertSame(
			$this->finder,
			$this->finder->setRelativeRootPath(''),
			'uses a fluent interface'
		);
		$this->assertEquals('', $this->finder->getRelativeRootPath());
	}

	/**
	 * @depends	testGetSetRelativeRootBasePathEnabled
	 * @return	null
	 */
	public function testResolveRootPathEmptyRelativeRoot()
	{
		$this->assertEquals('', $this->finder->getRelativeRootPath());
		$this->assertEquals(
			$this->finder->getBasePath(),
			$this->finder->resolveRootPath(),
			'no relative root means root base is base path when enabled'
		);

		$this->finder->disableBasePath();
		$this->assertEquals(
			'',
			$this->finder->resolveRootPath(),
			'no relative root and disable base means empty root path'
		);
	}

	/**
	 * @depends	testGetSetRelativeRootBasePathEnabled
	 * @return	null
	 */
	public function testResolveRootPathRelativeRootNotEmptyNoSlash()
	{
		$base = $this->finder->getBasePath();
		$relative = 'myRelative/root/path';
		$this->finder->setRelativeRootPath($relative);
		
		$this->finder->enableBasePath();
		$this->assertEquals(
			"$base/$relative",
			$this->finder->resolveRootPath()	
		);
		
		$this->finder->disableBasePath();
		$this->assertEquals(
			$relative,
			$this->finder->resolveRootPath()	
		);
	}

	/**
	 * Takes care of the begining slash so you don't have to worry about it
	 * 
	 * @depends	testGetSetRelativeRootBasePathEnabled
	 * @return	null
	 */
	public function testResolveRootPathRelativeRootNotEmptyWithSlash()
	{
		$base = $this->finder->getBasePath();
		$relative = '/myRelative/root/path';
		$this->finder->setRelativeRootPath($relative);
		
		$this->finder->enableBasePath();
		$this->assertEquals(
			"{$base}{$relative}",
			$this->finder->resolveRootPath()	
		);
		
		$this->finder->disableBasePath();
		$this->assertEquals(
			$relative,
			$this->finder->resolveRootPath()	
		);
	}

	/**
	 * By default getPath will resolve to the base path. Any path given 
	 * will be appended to base path unless you disable base path.
	 *
	 * @testInterface
	 * @return	null
	 */
	public function testGetPathDefaultEnableBasePath()
	{
		$base = $this->finder->getBasePath();
		$this->assertEquals($base, $this->finder->getPath());
		$this->assertEquals($base, $this->finder->getPath(''));
	
		$path = '/this/is/my/path';
		$expected = "$base$path";
		$this->finder->enableBasePath();
		$this->assertEquals($expected, $this->finder->getPath($path));		
	}

	/**
	 *
	 * @testGetPathDefaultEnableBasePath
	 * @return	null
	 */
	public function testGetPathDefaultDisableBasePath()
	{
		$this->finder->disableBasePath();
		$this->assertEquals('', $this->finder->getPath());
		$this->assertEquals('', $this->finder->getPath(''));
	
		$path = '/this/is/my/path';
		$this->assertEquals($path, $this->finder->getPath($path));		
	}

	/**
	 * @testInterface
	 * @return	null
	 */
	public function testGetPathEnableBasePathSetRelative()
	{
		$relative = 'this/is/my/root';
		$base = $this->finder->getBasePath();
		$this->finder->setRelativeRootPath($relative);

		$expected = "$base/$relative";
		$this->assertEquals($expected, $this->finder->getPath());
		$this->assertEquals($expected, $this->finder->getPath(''));
	
		$path = '/this/is/my/path';
		$expected = "$base/$relative$path";
		$this->assertEquals($expected, $this->finder->getPath($path));		
	}

	/**
	 * @testInterface
	 * @return	null
	 */
	public function testGetPathDisableBasePathSetRelative()
	{
		$this->finder->disableBasePath();

		$relative = 'this/is/my/root';
		$this->finder->setRelativeRootPath($relative);

		$this->assertEquals($relative, $this->finder->getPath());
		$this->assertEquals($relative, $this->finder->getPath(''));
	
		$path = '/this/is/my/path';
		$expected = "$relative$path";
		$this->assertEquals($expected, $this->finder->getPath($path));		
	}


	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetRelativeRootNotStringArray_Failure()
	{
		$this->finder->setRelativeRootPath(array(1,2,3));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetRelativeRootNotStringInt_Failure()
	{
		$this->finder->setRelativeRootPath(1232);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetRelativeRootNotStringFloat_Failure()
	{
		$this->finder->setRelativeRootPath(1.233);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetRelativeRootNotStringObj_Failure()
	{
		$this->finder->setRelativeRootPath(new StdClass());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetRelativeRootIsBasePath_Failure()
	{
		
		$this->finder->setRelativeRootPath(AF_BASE_PATH);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetRelativeRootContainsBasePath_Failure()
	{
		$path = AF_BASE_PATH . '/my/relative/path';
		$this->finder->setRelativeRootPath($path);
	}
}
