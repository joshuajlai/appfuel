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


}
