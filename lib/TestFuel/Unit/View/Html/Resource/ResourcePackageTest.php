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
	Appfuel\View\Html\Resource\ResourceVendor,
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
	 * @var PackageManifest
	 */
	protected $manifest = null;

	/**
	 * @var ResourceVendor
	 */
	protected $vendor = null;

	/**
	 * @var FileReader
	 */	
	protected $reader = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$vendor = array(
			'name'		=> 'aftest',
			'pkg-path'  => 'test/resource/aftest/html/pkg',
			'build-path'=> 'test/resource/aftest/build',
			'version'   => '0.0.1',
			'packages'  => array(
				'js-example'  => 'example-pkg',
			)
		);
		$this->vendor = new ResourceVendor($vendor);

		$manifest = array(
			'name' => 'js-example',
			'desc' => 'functional test module for appfuels build system',
			'dir'  => 'example-pkg',
			'files' => array(
				'js' => array(
					"src/js/file1.js",
					"src/js/file2.js",
					"src/js/file3.js"),
				'css' => array("src/css/file4.css","src/css/file5.css"),
				'asset' => array(
					"src/asset/blog-icon.png",
					"src/asset/warning-icon.png"
				),
			),
		);
		$this->manifest = new PackageManifest($manifest);

		$finder = new FileFinder();
		$this->reader = new FileReader($finder);

		$this->pkg = new ResourcePackage(
			$this->vendor,
			$this->manifest,
			$this->reader
		);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->vendor   = null;
		$this->manifest = null;
		$this->reader   = null;
		$this->pkg      = null;
	}

	/**
	 * @return	PackageFileList
	 */
	public function getResourcePackage()
	{
		return $this->pkg;
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

		$this->assertSame($this->vendor, $pkg->getVendor());
		$this->assertSame($this->manifest, $pkg->getManifest());
		$this->assertSame($this->reader, $pkg->getFileReader());
	}
}
