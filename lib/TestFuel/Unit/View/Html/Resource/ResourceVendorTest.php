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
	Appfuel\View\Html\Resource\ResourceVendor;


/**
 * The package manifest holds meta data, file list, tests and dependencies
 * for the package
 */
class ResourceVendorTest extends BaseTestCase
{
	/**
	 * @return	array
	 */
	public function provideInvalidArrayKeys()
	{
		return array(
			array(1234),
			array(0),
			array(1),
			array(1.23),
			array(true),
			array(false)
		);
	}

	/**
	 * @return	array
	 */
	public function provideValidVersions()
	{
		return array(
			array(0,		'0'),
			array(1,		'1'),
			array(-1,		'-1'),
			array(1234,		'1234'),
			array(-1234,	'-1234'),
			array(1.2,		'1.2'),
			array(0.0,		'0.0'),
			array(-0.1,		'-0.1'),
			array('0.0.123', '0.0.123'),
			array('1.1rc',	'1.1rc'),
			array(true,		'1'),
			array(false,	'')
		);
	}

	/**
	 * @return	array
	 */
	public function provideInvalidVersions()
	{
		return array(
			array(array(1,2,3)),
			array(new StdClass()),
		);
	}

	/**
	 * @return null
	 */
	public function testInitialState()
	{
		$data = array(
			'name'		 => 'my package',
			'desc'		 => 'description about the vendor',
			'pkg-path'	 => 'my/pkg/path',
			'build-path' => 'my/build/path',
			'version'	 => '0.0.1',
			'packages' => array(
				'pkg-1' =>'path/to/pkg-1',
				'pkg-2' => 'pkg-2',
				'pkg-3' => 'path/to/pkg3'
			)
		);
		
		$vendor = new ResourceVendor($data);
		$this->assertInstanceOf(
			'Appfuel\View\Html\Resource\ResourceVendorInterface',
			$vendor
		);

		$this->assertEquals($data['name'], $vendor->getVendorName());
		$this->assertEquals($data['desc'], $vendor->getVendorDescription());
		$this->assertEquals($data['pkg-path'], $vendor->getPackagePath());
		$this->assertEquals($data['build-path'], $vendor->getBuildPath());
		$this->assertEquals($data['packages'], $vendor->getPackages());
	}

	/**
	 * @return null
	 */
	public function testEmptyVendor()
	{
		$vendor = new ResourceVendor(array());
		$this->assertNull($vendor->getVendorName());
		$this->assertNull($vendor->getVendorDescription());
		$this->assertNull($vendor->getPackagePath());
		$this->assertNull($vendor->getBuildPath());
		$this->assertEquals(array(), $vendor->getPackages());
		
	}

    /**
     * @expectedException   InvalidArgumentException
     * @dataProvider        provideInvalidStrings
     * @return              null
     */
    public function testInvalidPackageName_Failure($name)
    {  
        $data = array('name' => $name);
        $vendor = new ResourceVendor($data);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @dataProvider        provideInvalidStrings
     * @return              null
     */
    public function testDescriptionInvalidString_Failure($desc)
    {
        $data = array('desc' => $desc);
        $vendor = new ResourceVendor($data);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @dataProvider        provideInvalidStrings
     * @return              null
     */
    public function testPackagePathInvalidString_Failure($path)
    {
        $data = array('pkg-path' => $path);
        $vendor = new ResourceVendor($data);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @return              null
     */
    public function testPackagePathEmptyString_Failure()
    {
        $data = array('pkg-path' => '');
        $vendor = new ResourceVendor($data);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @dataProvider        provideInvalidStrings
     * @return              null
     */
    public function testBuildPathInvalidString_Failure($path)
    {
        $data = array('build-path' => $path);
        $vendor = new ResourceVendor($data);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @return              null
     */
    public function testBuildPathEmptyString_Failure()
    {
        $data = array('build-path' => '');
        $vendor = new ResourceVendor($data);
    }

	/**
	 * @dataProvider	provideValidVersions
	 * @return			null
	 */
	public function testVersion($version, $expected)
	{
		$data = array('version' => $version);
        $vendor = new ResourceVendor($data);
	
		$this->assertEquals($expected, $vendor->getVersion());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidVersions
	 * @return				null
	 */
	public function testInvalidVersion_Failure($version)
	{
		$data = array('version' => $version);
		$vendor = new ResourceVendor($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testSetPackagesIndexArray()
	{
		$data = array('packages' => array('pkg1', 'pkg2', 'pkg3'));
		$vendor = new ResourceVendor($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testSetPackagesInvalidPath($path)
	{
		$data = array('packages' => array('key1' => $path, ''=>'pkg2'));
		$vendor = new ResourceVendor($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidArrayKeys
	 * @return				null
	 */
	public function testSetPackagesInvalidKey($key)
	{
		$data = array('packages' => array($key => 'path', ''=>'pkg2'));
		$vendor = new ResourceVendor($data);
	}
	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testSetPackagesEmptyKey()
	{
		$data = array('packages' => array('pkg'=>'pkg1', ''=>'pkg2'));
		$vendor = new ResourceVendor($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testSetPackagesEmptyPath()
	{
		$data = array('packages' => array('pkg'=>'', 'pkg2'=>'pkg2'));
		$vendor = new ResourceVendor($data);
	}


}
