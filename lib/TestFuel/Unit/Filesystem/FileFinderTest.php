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
namespace TestFuel\Unit\Filesystem;

use StdClass,
	SplFileInfo,
	Appfuel\Filesystem\FileFinder,
	TestFuel\TestCase\BaseTestCase;

/**
 */
class FileFinderTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var FileFinder
	 */
	protected $finder = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->finder = new FileFinder();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->finder = null;
	}

	/**
	 * @return	FileFinder
	 */
	public function getFinder()
	{
		return $this->finder;
	}

	/**
	 * @return	array
	 */
	public function provideNonStrings()
	{
		return array(
			array(1),
			array(-1),
			array(0),
			array(array(1,2,3)),
			array(new StdClass()),
			array(true),
			array(false)
		);
	}

	/**
	 * @return null
	 */
	public function testInitialState()
	{
		$finder = $this->getFinder();
		$this->assertInstanceOf(
			'Appfuel\Filesystem\FileFinderInterface',
			$finder
		);

		$this->assertTrue(defined('AF_BASE_PATH'));
		$this->assertEquals(AF_BASE_PATH, $finder->getBasePath());
		$this->assertEquals('', $finder->getRelativeRootPath());
	}
	
	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testConstructorNoBasePath()
	{
		$path = '/usr/local/share/pear';
		$finder = new FileFinder($path, false);

		$this->assertNull($finder->getBasePath());
		$this->assertEquals($path, $finder->getRelativeRootPath());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testConstructorNoBasePathNoRootPath()
	{
		$finder = new FileFinder(null, false);
		
		$this->assertNull($finder->getBasePath());
		$this->assertEquals('', $finder->getRelativeRootPath());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetRelativePath()
	{
		$finder = $this->getFinder();
		
		$root = 'resource/appfuel/sql';
		$this->assertNotEquals($root, $finder->getRelativeRootPath());
		$this->assertSame($finder, $finder->setRelativeRootPath($root));
		$this->assertEquals($root, $finder->getRelativeRootPath());

		$root = '';
		$this->assertSame($finder, $finder->setRelativeRootPath($root));
		$this->assertEquals($root, $finder->getRelativeRootPath());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideNonStrings
	 * @depends				testSetRelativePath
	 * @return				null
	 */
	public function testSetRelativeRootPathNotString_Failure($path)
	{
		$finder = $this->getFinder();
		$finder->setRelativeRootPath($path);
	}

	/**
	 * @expectedException	RunTimeException
	 * @depends				testSetRelativePath
	 * @return				null
	 */
	public function testSetRelativeRootWithBasePathWhenEnabled_Failure()
	{
		$path = AF_BASE_PATH . '/resource/appfuel/sql';
		
		$finder = $this->getFinder();
		$finder->setRelativeRootPath($path);	
	}

	/**
	 * With no relative root an no base path getPath will return any path
	 * given to it
	 *
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testGetPathNoNoBasePathRelativeRoot()
	{
		$finder = new FileFinder(null, false);
		$this->assertEquals('', $finder->getRelativeRootPath());
		$this->assertFalse($finder->isBasePath());
		
		$this->assertEquals('', $finder->getPath());

		$path = '/usr/local/share/pear/somefile.php';
		$this->assertEquals($path, $finder->getPath($path));
	}

	/**
	 * @dataProvider		provideNonStrings
	 * @depends				testSetRelativePath
	 * @return				null
	 */
	public function testGetPathInvalidString($path)
	{
		$finder = $this->getFinder();
		$this->assertFalse($finder->getPath($path));
	}

	/**
	 * @depends		testSetRelativePath
	 * @return		null
	 */
	public function testGetPathSplFileInfo()
	{
		$filePath  = 'path/to/myfile.php';
		$path = new SplFileInfo($filePath);
		$finder = $this->getFinder();
		
		$basePath = $finder->getBasePath();
		$expected = "{$basePath}/$filePath";
		$this->assertEquals($expected, $finder->getPath($path));

		$relativeRoot = 'resource/appfuel/sql';
		$finder->setRelativeRootPath($relativeRoot);
		
		$expected = "{$basePath}/{$relativeRoot}/{$filePath}";
		$this->assertEquals($expected, $finder->getPath($path));
	}

	/**
	 * @depends		testSetRelativePath
	 * @return		null
	 */
	public function testGetPathRelativeRootOnly()
	{
		$root   = 'resource/appfuel/sql';
		$finder = new FileFinder($root, false);
		$this->assertFalse($finder->isBasePath());

		$this->assertEquals($root, $finder->getPath());
	}

	/**
	 * @depends		testSetRelativePath
	 * @return		null
	 */
	public function testGetPathWithForwardSlash()
	{
		$root     = 'my/root/path';
		$myPath   = '/test/path/file.php';
		$finder   = $this->getFinder();
		$basePath = $finder->getBasePath();
		$finder->setRelativeRootPath($root);

		$expected = "$basePath/$root{$myPath}";
		$result = $finder->getPath($myPath);
		$this->assertEquals($expected, $finder->getPath($myPath));
	}

}
