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
	public function testNameAndInterface()
	{
		$data = array('name' => 'example_pkg');
		$manifest = new PackageManifest($data);
		$this->assertInstanceOf(
			'Appfuel\View\Html\Resource\PackageManifestInterface',
			$manifest
		);
		$this->assertEquals($data['name'], $manifest->getPackageName());
		$this->assertNull($manifest->getPackageDescription());
		$this->assertEquals('src', $manifest->getFileDir());
		$this->assertEquals('tests', $manifest->getTestDir());
	
		/* since none were defined all will return false */
		$this->assertFalse($manifest->getFiles('js'));
		$this->assertFalse($manifest->getTestFiles('js'));
		$this->assertEquals(array(), $manifest->getDependencies());
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
		$data = array('name' => 'pkg', 'desc' => 'my package description');
		$manifest = new PackageManifest($data);
		$this->assertEquals($data['desc'], $manifest->getPackageDescription());
	
		/* empty description is allowed */	
		$data = array('name' => 'pkg', 'desc' => '');
		$manifest = new PackageManifest($data);
		$this->assertEquals($data['desc'], $manifest->getPackageDescription());

		$data = array('name' => 'pkg', 'desc' => null);
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
		$data = array('name' => 'pkg', 'desc' => $desc);
		$manifest = new PackageManifest($data);
	}

	/**
	 * When no directory is given then the package name is assumed to be the
	 * directory name
	 *
	 * @return null
	 */
	public function testNoPacakgeDir()
	{
		$data = array('name' => 'pkg');
		$manifest = new PackageManifest($data);
		$this->assertEquals(
			$manifest->getPackageName(), 
			$manifest->getPackageDir()
		);
	}

	/**
	 * @return	null
	 */	
	public function testPackageDir()
	{
		$data = array('name' => 'pkg', 'dir' => 'my/dir');
		$manifest = new PackageManifest($data);
		$this->assertEquals('my/dir', $manifest->getPackageDir());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testPackageDirInvalidString_Failure($dir)
	{
		$data = array('name' => 'pkg', 'dir' => $dir);
		$manifest = new PackageManifest($data);
	
	}
	
	/**
	 * @return	null
	 */
	public function testFileWithOneTypeNoPath()
	{
		$data = array(
			'name' => 'pkg',
			'files' => array(
				'js' => array('file1', 'file2', 'file3')
			)
		);
		$manifest = new PackageManifest($data);
		$this->assertEquals('src', $manifest->getFileDir());
		$this->assertEquals(array('js'), $manifest->getFileTypes());
		$this->assertEquals($data['files']['js'], $manifest->getFiles('js'));
	}

	/**
	 * @return	null
	 */
	public function testFilessWithManyTypes()
	{
		$data = array(
			'name' => 'pkg',
			'files' => array(
				'js' => array('file1', 'file2', 'file3'),
				'css' => array('file4', 'file5'),
				'asset' => array('file6', 'file7'),
			)
		);
		$manifest = new PackageManifest($data);

		$expected = array('js', 'css', 'asset');
		$this->assertEquals('src', $manifest->getFileDir());
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
			'name' => 'pkg',
			'files' => array(
				'dir' => 'my/path',
				'js' => array('file1', 'file2', 'file3'),
				'css' => array('file4', 'file5'),
				'asset' => array('file6', 'file7'),
			)
		);
		$manifest = new PackageManifest($data);

		$expected = array('js', 'css', 'asset');
		$this->assertEquals('my/path', $manifest->getFileDir());
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
			'name' => 'pkg',
			'files' => array(
				'dir' => 'my/path'
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
		$data = array('name' => 'pkg', 'files' => array());
		$manifest = new PackageManifest($data);
		$this->assertEquals('src', $manifest->getFileDir());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidArray
	 * @return				null
	 */
	public function testSetFileInvalidArray_Failure($files)
	{
		$data = array('name' => 'pkg', 'files' => $files);
		$manifest = new PackageManifest($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidArray
	 * @return				null
	 */
	public function testSetTestsInvalidArray_Failure($files)
	{
		$data = array('name' => 'pkg', 'tests' => $files);
		$manifest = new PackageManifest($data);
	}

	/**
	 * @return	null
	 */
	public function testTestsWithOneTypeNoPath()
	{
		$data = array(
			'name' => 'pkg',
			'tests' => array(
				'js' => array('file1', 'file2', 'file3')
			)
		);
		$manifest = new PackageManifest($data);
		$this->assertEquals('tests', $manifest->getTestDir());
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
			'name' => 'pkg',
            'tests' => array(
                'dir' => 'my/path',
                'js' => array('file1', 'file2', 'file3'),
                'css' => array('file4', 'file5'),
                'asset' => array('file6', 'file7'),
            )
        );
        $manifest = new PackageManifest($data);

        $expected = array('js', 'css', 'asset');
        $this->assertEquals('my/path', $manifest->getTestDir());
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
		$data = array(
			'name' => 'pkg',	
			'depends' => array('my'=> array('package1','package2'))
		);
		$manifest = new PackageManifest($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testSetDependsEmptyString_Failure()
	{
		$data = array(
			'name' => 'pkg',
			'depends' => array('my/package', 'your/package', '')
		);
		$manifest = new PackageManifest($data);
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testSetDependsNotString($file)
	{
		$data = array(
			'name' => 'pkg',
			'depends' => array('my/package', 'your/package', $file)
		);
		$manifest = new PackageManifest($data);
	}
}
