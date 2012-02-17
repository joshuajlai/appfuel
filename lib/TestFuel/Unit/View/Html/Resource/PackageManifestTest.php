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
	Appfuel\View\Html\Resource\PackageManifest;


/**
 * The package manifest holds meta data, file list, tests and dependencies
 * for the package
 */
class PackageManifestTest extends BaseTestCase
{
    /**
     * @return  array
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
     * @return  array
     */
    public function provideInvalidArray()
    {
        return array(
            array(12345),
            array(1.234),
            array(true),
            array(false),
            array(''),
			array('this is a string'),
            array(new StdClass())
        );
    }


	/**
	 * @return null
	 */
	public function testInitialState()
	{
		$data = array(
			'name'  => 'my package',
			'desc'  => 'my description',
			'files' => array(
				'js'  => array('src/filea.js', 'src/fileb.js'),
				'css' => array('src/filec.js', 'src/filed.js'),
				'img' => array('asset/image.png', 'asset/my.gif')
			),
			'tests' => array(
				'html'  => array('test/file.html', 'test/other.html'),
				'phtml' => array('test/other.phtml'),
				'js'    => array('test/filtest.js'),
			),
			'depends' => array(
				'my/package',
				'your/package',
				'our/package'
			)
		);
		
		$manifest = new PackageManifest($data);
		$this->assertInstanceOf(
			'Appfuel\View\Html\Resource\PackageManifestInterface',
			$manifest
		);

		$this->assertEquals($data['name'], $manifest->getPackageName());
		$this->assertEquals($data['desc'], $manifest->getPackageDescription());

		$this->assertEquals($data['files']['js'],  $manifest->getFiles('js'));
		$this->assertEquals($data['files']['css'], $manifest->getFiles('css'));
		$this->assertEquals($data['files']['img'], $manifest->getFiles('img'));
		$this->assertEquals($data['files'], $manifest->getAllFiles());
	
		$this->assertEquals(
			$data['tests']['html'],  
			$manifest->getTestFiles('html')
		);
		$this->assertEquals($data['tests'], $manifest->getAllTestFiles());
		$this->assertEquals($data['depends'], $manifest->getDependencies());
	}

	/**
	 * @return	null
	 */
	public function testDependenciesOnlyName()
	{
		$manifest = new PackageManifest(array());
		$this->assertNull($manifest->getPackageName());
		$this->assertNull($manifest->getPackageDescription());
		$this->assertEquals(array(), $manifest->getAllFiles());
		$this->assertEquals(array(), $manifest->getAllTestFiles());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidString
	 * @return				null
	 */
	public function testInvalidPackageName_Failure($name)
	{
		$data = array('name' => $name);
		$manifest = new PackageManifest($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidString
	 * @return				null
	 */
	public function testDescriptionInvalidString_Failure($desc)
	{
		$data = array('desc' => $desc);
		$manifest = new PackageManifest($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidArray
	 * @return				null
	 */
	public function testSetFileInvalidArray_Failure($files)
	{
		$data = array('files' => $files);
		$manifest = new PackageManifest($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidArray
	 * @return				null
	 */
	public function testSetTestsInvalidArray_Failure($files)
	{
		$data = array('tests' => $files);
		$manifest = new PackageManifest($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testSetDependsAssocArray_Failure()
	{
		$data = array('depends' => array('my'=> array('package1','package2')));
		$manifest = new PackageManifest($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testSetDependsEmptyString_Failure()
	{
		$data = array('depends' => array('my/package', 'your/package', ''));
		$manifest = new PackageManifest($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidString
	 * @return				null
	 */
	public function testSetDependsNotString($file)
	{
		$data = array('depends' => array('my/package', 'your/package', $file));
		$manifest = new PackageManifest($data);
	}
}
