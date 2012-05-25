<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace TestFuel\Unit\Html\Resource;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Html\Resource\FileStack;


/**
 * The package file list holds a list of files categories by file type
 */
class FileStackTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var FileStack
	 */
	protected $stack = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->stack = new FileStack();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->stack = null;
	}

	/**
	 * @return	PackageFileList
	 */
	public function getFileStack()
	{
		return $this->stack;
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
		$list = $this->getFileStack();
		$this->assertInstanceOf(
			'Appfuel\Html\Resource\FileStackInterface',
			$list
		);

		$this->assertEquals(array(), $list->getAll());
		$this->assertEquals(array(), $list->getTypes());

		/* common types don't exist yet */
		$this->assertFalse($list->get('js'));
		$this->assertFalse($list->get('css'));
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testGetAdd()
	{
		$type = 'js';
		$file1 = 'src/my-file1.js';
		$file2 = 'src/my-file2.js';
		$file3 = 'src/my-file3.js';

		$list = $this->getFileStack();
		$this->assertEquals(false, $list->get('js'));
		$this->assertSame($list, $list->add($type, $file1));
	
		$expected = array($file1);
		$this->assertEquals($expected, $list->get('js'));
		
		/* duplicates are ignored */
		$this->assertSame($list, $list->add($type, $file1));
		$this->assertSame($list, $list->add($type, $file1));
		$this->assertSame($list, $list->add($type, $file1));

		$this->assertEquals($expected, $list->get('js'));
		
		$this->assertSame($list, $list->add($type, $file2));
		$this->assertSame($list, $list->add($type, $file3));
			
		$expected[] = $file2;
		$expected[] = $file3;
		$this->assertEquals($expected, $list->get('js'));
	
		$expected = array($type => $expected);
		$this->assertEquals($expected, $list->getAll());
	}

	/**
	 * @depends	testGetAdd
	 * @return	null
	 */
	public function testAddMultipleTypes()
	{
		$type1 = 'js';
		$file1 = 'src/myfile.js';
		$file2 = 'src/yourfile.js';

		$type2 = 'css';
		$file3 = 'src/myfile.css';
		$file4 = 'src/yourfile.css';

		$list = $this->getFileStack();
		$list->add($type1, $file1)
			 ->add($type1, $file2)
			 ->add($type2, $file3)
			 ->add($type2, $file4);	

		$expected = array(
			'js'  => array($file1, $file2),
			'css' => array($file3, $file4)
		);

		$this->assertEquals($expected['js'], $list->get('js'));
		$this->assertEquals($expected['css'], $list->get('css'));
		$this->assertEquals($expected, $list->getAll());	
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidString
	 * @depends				testGetAdd
	 * @return				null
	 */
	public function testAddTypeInvalidString_Failure($type)
	{
		$list = $this->getFileStack();
		$list->add($type, 'my/file');
	}

    /**
     * @expectedException   InvalidArgumentException
     * @depends             testGetAdd
     * @return              null
     */
	public function testAddTypeEmptyString()
	{
		$list = $this->getFileStack();
		$list->add('', 'my/file');
	}

    /**
     * @depends testInitialState
     * @return  null
     */
	public function testLoadWhenEmpty()
	{
		$list = $this->getFileStack();
		$this->assertFalse($list->get('js'));
		
		$files = array('js' => array('my/file.js', 'your/file.js'));
		$this->assertSame($list, $list->load($files));
	
		$this->assertEquals($files, $list->getAll());	
	}

	/**
     * @depends testLoadWhenEmpty
     * @return  null
     */
	public function testLoadWhenEmptyMultileTypes()
	{
		$list = $this->getFileStack();
		$this->assertFalse($list->get('js'));
		$this->assertFalse($list->get('css'));
		
		$files = array(
			'js'  => array('my/file.js', 'your/file.js'),
			'css' => array('my/other.css', 'my/more.css')
		);
		$this->assertSame($list, $list->load($files));
		$this->assertEquals($files, $list->getAll());	
	}

	/**
     * @depends testLoadWhenEmpty
     * @return  null
     */
	public function testLoadWhenEmptyListItemIsString()
	{
		$list = $this->getFileStack();
		$this->assertFalse($list->get('js'));
		$this->assertFalse($list->get('css'));
		
		$files = array(
			'js'  => 'my/file.js',
			'css' => 'my/other.css',
		);
		$this->assertSame($list, $list->load($files));

		/* will add the single file to the array */	
		$expected = array(
			'js'  => array('my/file.js'),
			'css' => array('my/other.css')
		);
		$this->assertEquals($expected, $list->getAll());	
	}

	/**
	 * @depends	testLoadWhenEmpty
	 * @return	null
	 */
	public function testLoadWithMoreThanOneCall()
	{
		$list = $this->getFileStack();
		$this->assertFalse($list->get('js'));
		$this->assertFalse($list->get('css'));
		
		$files1 = array(
			'js'  => array('my/file1.js', 'your/file1.js'),
			'css' => array('my/other1.css', 'my/more1.css')
		);

		$files2 = array(
			'js'  => array('my/file2.js', 'your/file2.js'),
			'css' => array('my/other2.css', 'my/more2.css')
		);
		$this->assertSame($list, $list->load($files1));
		$this->assertEquals($files1, $list->getAll());
		
		$this->assertSame($list, $list->load($files2));

		$expected = array(
			'js'  => array(
				'my/file1.js', 
				'your/file1.js',
				'my/file2.js', 
				'your/file2.js'
				),
			'css' => array(
				'my/other1.css', 
				'my/more1.css',
				'my/other2.css', 
				'my/more2.css'
			)
		);
		$this->assertEquals($expected, $list->getAll());
	}

	/**
	 * @expectedException	InvalidArgumentException
     * @depends				testLoadWhenEmpty
     * @return				null
     */
	public function testLoadNotAssociativeArray()
	{
		$list = $this->getFileStack();
		$files = array('my/file.js','my/other.css');

		$list->load($files);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testClearAll()
	{
		$list = $this->getFileStack();
		$files = array(
			'js'  => 'my/file.js',
			'css' => 'my/other.css',
		);

		$list->load($files);
		$this->assertSame($list, $list->clear());
		$this->assertEquals(array(), $list->getAll());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetWhenEmpty()
	{
		$list = $this->getFileStack();
		
		$files = array(
			'js'  => array('my/file.js', 'your/file.js'),
			'css' => array('my/other.css', 'my/more.css')
		);

		$this->assertSame($list, $list->set($files));
		$this->assertEquals($files, $list->getAll());
	}

	/**
	 * @depends	testLoadWhenEmpty
	 * @return	null
	 */
	public function testSetWithMoreThanOneCall()
	{
		$list = $this->getFileStack();
		
		$files1 = array(
			'js'  => array('a.js', 'b.js'),
			'css' => array('c.css', 'd.css')
		);
		$this->assertSame($list, $list->set($files1));
		$this->assertEquals($files1, $list->getAll());

		$files2 = array(
			'js'  => array('x.js', 'y.js'),
			'css' => array('w.css', 'z.css')
		);
		$this->assertSame($list, $list->set($files2));
		$this->assertEquals($files2, $list->getAll());
	}
}
