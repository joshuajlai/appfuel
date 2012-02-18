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
namespace TestFuel\Unit\View\Html\Resource;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileReader,
	Appfuel\View\Html\Resource\PackageManifest,
	Appfuel\View\Html\Resource\ResourcePackage;


/**
 * The resource package uses the manifest and file reader to gather the 
 * contents of the files stored in the manifest
 */
class ResourcePackageTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var ResourcePackage
	 */
	protected $pkg = null;
	
	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->pkg = new ResourcePackage();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->list = null;
	}

	/**
	 * @return	PackageFileList
	 */
	public function getResourcePackage()
	{
		return $this->pkg;
	}

	/**
	 * @return	array
	 */
	public function provideInvalidString()
	{
		return array(
			array(12345),
			array(1.234),
			array(true),
			array(false),
			array(array(1,2,3)),
			array(new StdClass())
		);
	}

	/**
	 * @return null
	 */
	public function testInitialState()
	{
		$pkg = $this->getResourcePackage();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Resource\ResourcePackageInterface',
			$pkg
		);
		$this->assertFalse($pkg->isManifest());
		$this->assertFalse($pkg->isFileReader());
		$this->assertNull($pkg->getFileReader());
		$this->assertNull($pkg->getManifest());
		$this->assertEquals('resource', $pkg->getResourceDir());
		$this->assertEquals('', $pkg->getVersion());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testGetSetResourceDir()
	{
		$dir = 'mydir';
		$pkg = $this->getResourcePackage();
		$this->assertSame($pkg, $pkg->setResourceDir($dir));
		$this->assertEquals($dir, $pkg->getResourceDir());

		$dir = 'test/resource';
		$this->assertSame($pkg, $pkg->setResourceDir($dir));
		$this->assertEquals($dir, $pkg->getResourceDir());
	
		$dir = '';
		$this->assertSame($pkg, $pkg->setResourceDir($dir));
		$this->assertEquals($dir, $pkg->getResourceDir());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidString
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetResourceDirInvalidStr_Failure($dir)
	{
		$pkg = $this->getResourcePackage();
		$pkg->setResourceDir($dir);	
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetVersion()
	{
		$version = 'my-version-string';
		$pkg = $this->getResourcePackage();
		$this->assertSame($pkg, $pkg->setVersion($version));
		$this->assertEquals($version, $pkg->getVersion());

	
		$version = '';
		$this->assertSame($pkg, $pkg->setVersion($version));
		$this->assertEquals($version, $pkg->getVersion());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetVersionInt()
	{
		$version = 1;
		$pkg = $this->getResourcePackage();
		$this->assertSame($pkg, $pkg->setVersion($version));
		$this->assertEquals((string)$version, $pkg->getVersion());

		$version = 0;
		$this->assertSame($pkg, $pkg->setVersion($version));
		$this->assertEquals((string)$version, $pkg->getVersion());

		$version = -123;
		$this->assertSame($pkg, $pkg->setVersion($version));
		$this->assertEquals((string)$version, $pkg->getVersion());

		$version = 123456;
		$this->assertSame($pkg, $pkg->setVersion($version));
		$this->assertEquals((string)$version, $pkg->getVersion());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetVersionFloat()
	{
		$version = 0.123;
		$pkg = $this->getResourcePackage();
		$this->assertSame($pkg, $pkg->setVersion($version));
		$this->assertEquals('0.123', $pkg->getVersion());

		$version = 123.123;
		$pkg = $this->getResourcePackage();
		$this->assertSame($pkg, $pkg->setVersion($version));
		$this->assertEquals('123.123', $pkg->getVersion());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetVersionArray_Failure()
	{
		$pkg = $this->getResourcePackage();
		$pkg->setVersion(array(1,2,3));	
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testSetVersionObject_Failuret()
	{
		$pkg = $this->getResourcePackage();
		$pkg->setVersion(new StdClass());	
	}

	/**
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testReader()
	{
		$pkg = $this->getResourcePackage();
		$reader = $this->getMock('Appfuel\Filesystem\FileReaderInterface');
		$this->assertFalse($pkg->isFileReader());
		$this->assertSame($pkg, $pkg->setFileReader($reader));
		$this->assertSame($reader, $pkg->getFileReader());
		$this->assertTrue($pkg->isFileReader());	
	}

	/**
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testManifest()
	{
		$pkg = $this->getResourcePackage();
		$interface = 'Appfuel\View\Html\Resource\PackageManifestInterface';
		$manifest = $this->getMock($interface);
		$this->assertFalse($pkg->isManifest());
		$this->assertSame($pkg, $pkg->setManifest($manifest));
		$this->assertSame($manifest, $pkg->getManifest());
		$this->assertTrue($pkg->isManifest());	
	}
}
