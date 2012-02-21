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
	 * @return	null
	 */
	public function testEmptyAndInterface()
	{
		$manifest = new PackageManifest(array());
		$this->assertInstanceOf(
			'Appfuel\View\Html\Resource\PackageManifestInterface',
			$manifest
		);
		$this->assertNull($manifest->getPackageName());
		$this->assertNull($manifest->getPackageDescription());
		$this->assertEquals('src', $manifest->getFilePath());
		$this->assertEquals('tests', $manifest->getTestPath());
	
		/* since none were defined all will return false */
		$this->assertFalse($manifest->getFiles('js'));
		$this->assertFalse($manifest->getTestFiles('js'));
		$this->assertEquals(array(), $manifest->getDependencies());
	}

	/**
	 * @return null
	 */
	public function testName()
	{
		$data = array('name' => 'my package');
		$manifest = new PackageManifest($data);
		$this->assertEquals($data['name'], $manifest->getPackageName());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testInvalidPackageName_Failure($name)
	{
		$data = array('name' => $name);
		$manifest = new PackageManifest($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */	
	public function testInvalidPackageNameEmptyString_Failure()
	{
		$data = array('name' => '');
		$manifest = new PackageManifest($data);
	}

	/**
	 * @return null
	 */
	public function testDescription()
	{
		$data = array('desc' => 'my package description');
		$manifest = new PackageManifest($data);
		$this->assertEquals($data['desc'], $manifest->getPackageDescription());
	
		/* empty description is allowed */	
		$data = array('desc' => '');
		$manifest = new PackageManifest($data);
		$this->assertEquals($data['desc'], $manifest->getPackageDescription());

		$data = array('desc' => null);
		$manifest = new PackageManifest($data);
		$this->assertEquals($data['desc'], $manifest->getPackageDescription());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testDescriptionInvalidString_Failure($desc)
	{
		$data = array('desc' => $desc);
		$manifest = new PackageManifest($data);
	}

	/**
	 * @return	null
	 */
	public function testFileWithOneTypeNoPath()
	{
		$data = array(
			'files' => array(
				'js' => array('file1', 'file2', 'file3')
			)
		);
		$manifest = new PackageManifest($data);
		$this->assertEquals('src', $manifest->getFilePath());
		$this->assertEquals(array('js'), $manifest->getFileTypes());
		$this->assertEquals($data['files']['js'], $manifest->getFiles('js'));
	}

	/**
	 * @return	null
	 */
	public function testFilesWithManyTypes()
	{
		$data = array(
			'files' => array(
				'js' => array('file1', 'file2', 'file3'),
				'css' => array('file4', 'file5'),
				'asset' => array('file6', 'file7'),
			)
		);
		$manifest = new PackageManifest($data);

		$expected = array('js', 'css', 'asset');
		$this->assertEquals('src', $manifest->getFilePath());
		$this->assertEquals($expected, $manifest->getFileTypes());
		$this->assertEquals($data['files']['js'], $manifest->getFiles('js'));
		$this->assertEquals($data['files']['css'], $manifest->getFiles('css'));
		$this->assertEquals(
			$data['files']['asset'], 
			$manifest->getFiles('asset')
		);
	}

	/**
	 * @return	null
	 */
	public function testFilesWithManyTypesWithPath()
	{
		$data = array(
			'files' => array(
				'path' => 'my/path',
				'js' => array('file1', 'file2', 'file3'),
				'css' => array('file4', 'file5'),
				'asset' => array('file6', 'file7'),
			)
		);
		$manifest = new PackageManifest($data);

		$expected = array('js', 'css', 'asset');
		$this->assertEquals('my/path', $manifest->getFilePath());
		$this->assertEquals($expected, $manifest->getFileTypes());
		$this->assertEquals($data['files']['js'], $manifest->getFiles('js'));
		$this->assertEquals($data['files']['css'], $manifest->getFiles('css'));
		$this->assertEquals(
			$data['files']['asset'], 
			$manifest->getFiles('asset')
		);
	}

	/**
	 * If you specify files you must have some file typs
	 * 
	 * @expectedException	InvalidArgumentException
	 * @return	null
	 */
	public function testFileWithPathOnly()
	{
		$data = array(
			'files' => array(
				'path' => 'my/path'
			)
		);
		$manifest = new PackageManifest($data);
	}

	/**
	 * Files must be an associative array of type=>array(file,list)
	 *
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testEmptyFileNoFilePath()
	{
		$data = array('files' => array());
		$manifest = new PackageManifest($data);
		$this->assertEquals('src', $manifest->getFilePath());
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
	 * @return	null
	 */
	public function testTestsWithOneTypeNoPath()
	{
		$data = array(
			'tests' => array(
				'js' => array('file1', 'file2', 'file3')
			)
		);
		$manifest = new PackageManifest($data);
		$this->assertEquals('tests', $manifest->getTestPath());
		$this->assertEquals(array('js'), $manifest->getTestFileTypes());
		$this->assertEquals(
			$data['tests']['js'], 
			$manifest->getTestFiles('js')
		);
	}

    /**
     * @return  null
     */
    public function testTestFilesWithManyTypesWithPath()
    {
        $data = array(
            'tests' => array(
                'path' => 'my/path',
                'js' => array('file1', 'file2', 'file3'),
                'css' => array('file4', 'file5'),
                'asset' => array('file6', 'file7'),
            )
        );
        $manifest = new PackageManifest($data);

        $expected = array('js', 'css', 'asset');
        $this->assertEquals('my/path', $manifest->getTestPath());
        $this->assertEquals($expected, $manifest->getTestFileTypes());
        $this->assertEquals(
			$data['tests']['js'], 
			$manifest->getTestFiles('js')
		);

        $this->assertEquals(
			$data['tests']['css'], 
			$manifest->getTestFiles('css')
		);
        $this->assertEquals(
            $data['tests']['asset'],
            $manifest->getTestFiles('asset')
        );
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
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testSetDependsNotString($file)
	{
		$data = array('depends' => array('my/package', 'your/package', $file));
		$manifest = new PackageManifest($data);
	}
}
