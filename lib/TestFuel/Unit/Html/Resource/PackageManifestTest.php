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
namespace TestFuel\Unit\Html\Resource;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Html\Resource\PackageManifest;

/**
 * The package manifest holds meta data, file list, tests and dependencies
 * for the package
 */
class PackageManifestTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var PackageManifest
	 */
	protected $manifest = null;

	/**
	 * @var array
	 */
	protected $pkgData = array(
		'name' => 'my-package',
		'desc' => 'A example package for testing does not really exist',
		'src'  => array(
			'dir' => 'alternate-dir',
			'build-file' => 'my_package',
			'files' => array(
				'js'	=> array('js/file_a.js', 'js/file_b.js'),
				'css'	=> array('css/file_c.css', 'js/file_d.css'),
				'asset' => array('asset/image_a.gif', 'asset/image_b.png') 
			),
			'depends' => array(
				'yui3'		=> array('node', 'event'),
				'fuelcell'	=> array('kernel-core', 'kernel-io')
			),
		),
		'test' => array(
			'dir' => 'alternate-test-dir',
			'build-file' => 'test_my_package',
			'files' => array(
				'js'   => array('my-testcase.js'),
				'html' => array('test-runner.html'),
			),
			'depends' => array(
				'yui3'		=> array('yui-test', 'console'),
				'fuelcell'	=> array('test-runner') 
			),
		),
	);

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->manifest = new PackageManifest($this->getPackageData());
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->manifest = null;
	}

	/**
	 * @return	PackageManifest
	 */
	public function getPackageManifest()
	{
		return $this->manifest;
	}

	/**
	 * @return	array
	 */
	public function getPackageData()
	{
		return $this->pkgData;
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$manifest = $this->getPackageManifest();
		$this->assertInstanceOf(
			'Appfuel\Html\Resource\PackageManifestInterface',
			$manifest
		);
	}
}
