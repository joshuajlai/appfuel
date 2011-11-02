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
namespace TestFuel\Test\Kernal\Dependency;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Kernal\Dependency\ClassDependency;

/**
 * The class dependency is used to declare a group of files or namespaces that
 * should be loaded by a dependency loader that does not use the autoloader.
 */
class ClassDependencyTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var ClassDependency
	 */
	protected $dependency = null;

	/**
	 * Only parameter used in the constructor. It is used to prefix to 
	 * the namespaces after they are resolved.
	 * @var string
	 */
	protected $rootPath = null;
	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->rootPath = '/my/root/path';
		$this->dependency = new ClassDependency($this->rootPath);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->dependency = null;
	}
	
	/**
	 * @return	array
	 */
	public function provideInvalidStrings()
	{
		return array(
			array(''),
			array(' '),
			array("\n"),
			array(" \t"),
			array(" \t\n"),
			array(array()),
			array(array(1,2,3)),
			array(array('this\is\my\namespace')),
			array(new StdClass()),
			array(12345),
			array(1.234)
		);
	}

    /**
     * @return null
     */
    public function testInterface()
    {
        $this->assertInstanceOf(
            'Appfuel\Kernal\Dependency\ClassDependencyInterface',
            $this->dependency
        );
    }

	/**
	 * An immutable member, the root path is used by the dependency loader to
	 * prefix to the resolved namespace to produce an absolute path to the 
	 * file. The check if the file exists is done in the loader so we only
	 * care that its a valid string thats not empty
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testRootPath()
	{
		$this->assertEquals($this->rootPath, $this->dependency->getRootPath());
	}

	/**
	 * No check other than a non empty string is needed on the namespace
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAddNamespaceGetNamespaces()
	{
		/* default value */
		$expected = array();
		$this->assertEquals($expected, $this->dependency->getNamespaces());

		$ns1 = 'My\Namespace\A';
		$ns2 = 'My\Namespace\B';
		$ns3 = 'Other\Namespace\C';
		$this->assertSame(
			$this->dependency,
			$this->dependency->addNamespace($ns1),
			'uses fluent interface'
		);
		$expected[] = $ns1;
		$this->assertEquals($expected, $this->dependency->getNamespaces());
	
		$this->assertSame(
			$this->dependency,
			$this->dependency->addNamespace($ns2)
		);
		$expected[] = $ns2;
		$this->assertEquals($expected, $this->dependency->getNamespaces());
	
		$this->assertSame(
			$this->dependency,
			$this->dependency->addNamespace($ns3)
		);
		$expected[] = $ns3;
		$this->assertEquals($expected, $this->dependency->getNamespaces());

		/* duplicates are removed */
		$this->assertSame(
			$this->dependency,
			$this->dependency->addNamespace($ns2)
		);
		$this->assertEquals($expected, $this->dependency->getNamespaces());
			
		$this->dependency->addNamespace($ns1)
						 ->addNamespace($ns2)
						 ->addNamespace($ns3);
		
		$this->assertEquals($expected, $this->dependency->getNamespaces());
	}

	/**
	 * Empty strings or anything that is not a string is ignored without error
	 *
	 * @dataProvider	provideInvalidStrings
	 * @depends			testAddNamespaceGetNamespaces
	 * @return		null
	 */
	public function testAddNamespaceInvalidNamespaces($namespace)
	{
		$this->assertSame(
			$this->dependency,
			$this->dependency->addNamespace($namespace),
			'uses a fluent interface'
		);
		$this->assertEquals(array(), $this->dependency->getNamespaces());
	}

	/**
	 * @depends	testAddNamespaceGetNamespaces
	 * @return	null
	 */
	public function testLoadNamespaces()
	{
		$namespaces = array(
			'My\Namespace\A',
			'My\Namespace\B',
			'Other\Namespace\A'
		);
		$this->assertSame(
			$this->dependency,
			$this->dependency->loadNamespaces($namespaces),
			'uses fluent interface'
		);
		$this->assertEquals($namespaces, $this->dependency->getNamespaces());

		/* no duplicates are loaded */
		$namespaces[] = 'New\Namespace\X';
		$this->assertSame(
			$this->dependency,
			$this->dependency->loadNamespaces($namespaces),
			'uses fluent interface'
		);
		$this->assertEquals($namespaces, $this->dependency->getNamespaces());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAddFileGetFiles()
	{
		/* default value */
		$expected = array();
		$this->assertEquals($expected, $this->dependency->getFiles());

		$file1 = 'myfile.php';
		$file2 = 'my/other/file.php';
		$file3 = 'yet/another/file.php';
		$this->assertSame(
			$this->dependency,
			$this->dependency->addFile($file1),
			'uses fluent interface'
		);
		$expected[] = $file1;
		$this->assertEquals($expected, $this->dependency->getFiles());
	
		$this->assertSame(
			$this->dependency,
			$this->dependency->addFile($file2)
		);
		$expected[] = $file2;
		$this->assertEquals($expected, $this->dependency->getFiles());
	
		$this->assertSame(
			$this->dependency,
			$this->dependency->addFile($file3)
		);
		$expected[] = $file3;
		$this->assertEquals($expected, $this->dependency->getFiles());

		/* duplicates are removed */
		$this->assertSame(
			$this->dependency,
			$this->dependency->addFile($file2)
		);
		$this->assertEquals($expected, $this->dependency->getFiles());
			
		$this->dependency->addFile($file1)
						 ->addFile($file2)
						 ->addFile($file3);
		
		$this->assertEquals($expected, $this->dependency->getFiles());
	}

	/**
	 * @depends	testAddFileGetFiles
	 * @return	null
	 */
	public function testLoadFiles()
	{
		$files = array(
			'my/file.php',
			'my/other/file.php',
			'my/yet/another/file.php'
		);
		$this->assertSame(
			$this->dependency,
			$this->dependency->loadFiles($files),
			'uses fluent interface'
		);
		$this->assertEquals($files, $this->dependency->getFiles());

		/* no duplicates are loaded */
		$files[] = 'one/more/file.php';
		$this->assertSame(
			$this->dependency,
			$this->dependency->loadFiles($files),
			'uses fluent interface'
		);
		$this->assertEquals($files, $this->dependency->getFiles());
	}

	/**
	 * Empty strings or anything that is not a string is ignored without error
	 *
	 * @dataProvider	provideInvalidStrings
	 * @depends			testAddFileGetFiles
	 * @return		null
	 */
	public function testAddFilesInvalidFiles($file)
	{
		$this->assertSame(
			$this->dependency,
			$this->dependency->addFile($file),
			'uses a fluent interface'
		);
		$this->assertEquals(array(), $this->dependency->getFiles());
	}

	/**
	 * @return	null
	 */
	public function testConstructRootPath()
	{
		/* empty string is allowed */
		$path = '';
		$dependency = new ClassDependency($path);
		$this->assertEquals($path, $dependency->getRootPath());

		/* any padding will be trimed */
		$path = ' /root/path';
		$dependency = new ClassDependency($path);
		$this->assertEquals(trim($path), $dependency->getRootPath());

		$path = "\t /root/path/ \t";
		$dependency = new ClassDependency($path);
		$this->assertEquals(trim($path), $dependency->getRootPath());
	}


	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function testConstructInvalidRootPath_ArrayFailure()
	{
		$dependency = new ClassDependency(array(1,3,4));
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function testConstructInvalidRootPath_IntFailure()
	{
		$dependency = new ClassDependency(1234);
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return				null
	 */
	public function testConstructInvalidRootPath_ObjectFailure()
	{
		$dependency = new ClassDependency(new StdClass());
	}



}
