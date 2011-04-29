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
namespace Test\Appfuel\App\View;

use Test\AfTestCase as ParentTestCase,
	Appfuel\App\View\Template,
	StdClass;

/**
 * The view template is an extension of view data that adds on the ability 
 * to have template files the may or may not get the data in the templates
 * dictionary.
 */
class TemplateTest extends ParentTestCase
{
	/**
	 * System under test
	 * @var Template
	 */
	protected $template = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->template = new Template();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->template);
	}

	/**
	 * Make sure that template extends from view data
	 *
	 * @return null
	 */
	public function testConstructor()
	{
		$this->assertInstanceOf(
			'Appfuel\App\View\Data',
			$this->template,
			'Template must be a view data object'
		);
	}

	/**
	 * AddFile takes two parameters: the key to find it by and the file
	 * file its self. The second parameter can either be a path to the file;
	 * a string. Or it can be a file object of type 
	 * Appfuel\Framework\FileInterface. addFile is a fluent interface so it
	 * returns back a reference to the Template.
	 *
	 * @return null
	 */
	public function testAddingFileGetFileFileExistsAsString()
	{
		$file = 'path/to/some/where';
		$key  = 'my-file';
		
		$this->assertFalse($this->template->fileExists($key));
		$result = $this->template->addFile($key, $file);
		$this->assertSame(
			$this->template,
			$result,
			'addFile uses a fluent interface'
		);

		$this->assertTrue($this->template->fileExists($key));
		$this->assertEquals($file, $this->template->getFile($key));

		/* add a second file to see the effect */
		$file2 = 'path/to/some/where/else';
		$key2  = 'my-seconf-file';
		
		$this->assertFalse($this->template->fileExists($key2));
		$result = $this->template->addFile($key2, $file2);
		$this->assertSame(
			$this->template,
			$result,
			'addFile uses a fluent interface'
		);

		$this->assertTrue($this->template->fileExists($key));
		$this->assertEquals($file, $this->template->getFile($key));
		$this->assertTrue($this->template->fileExists($key2));
		$this->assertEquals($file2, $this->template->getFile($key2));

		/* add a third file with a key as an integer */
		$file3 = 'other/path';
		$key3  = 22;
		
		$this->assertFalse($this->template->fileExists($key3));
		$result = $this->template->addFile($key3, $file3);
		$this->assertSame(
			$this->template,
			$result,
			'addFile uses a fluent interface'
		);

		$this->assertTrue($this->template->fileExists($key3));
		$this->assertEquals($file3, $this->template->getFile($key3));
	}

	/**
	 * AddFile's second parameter also accepts a file object itself so we
	 * will test that
	 *
	 * @return null
	 */
	public function testAddingFileGetFileFileExistsAsFileInterface()
	{
		$file = $this->getMock('\Appfuel\Framework\FileInterface');
		$key  = 'my-file';
		$this->assertFalse($this->template->fileExists($key));
		$result = $this->template->addFile($key, $file);
		$this->assertSame(
			$this->template,
			$result,
			'addFile uses a fluent interface'
		);

		$this->assertTrue($this->template->fileExists($key));
		$this->assertEquals($file, $this->template->getFile($key));

		/* add a second file to see the effect */
		$file2 = $this->getMock('Appfuel\Framework\FileInterface');
		$key2  = 'my-seconf-file';
		
		$this->assertFalse($this->template->fileExists($key2));
		$result = $this->template->addFile($key2, $file2);
		$this->assertSame(
			$this->template,
			$result,
			'addFile uses a fluent interface'
		);

		$this->assertTrue($this->template->fileExists($key));
		$this->assertEquals($file, $this->template->getFile($key));
		$this->assertTrue($this->template->fileExists($key2));
		$this->assertEquals($file2, $this->template->getFile($key2));
	}

	/**
	 * @return null
	 */
	public function testGetFileThatDoesNotExist()
	{
		$this->assertFalse($this->template->fileExists('no-key'));
		$this->assertFalse($this->template->getFile('no-key'));
	}
	
	/**
	 * When you add the same key twice the old value for that key gets 
	 * overwritten with the new value.
	 *
	 * @return null
	 */
	public function testAddSameFileTwice()
	{
		$file = 'path/to/some/where';
		$key  = 'my-file';
		
		$this->assertFalse($this->template->fileExists($key));
		$this->template->addFile($key, $file);
		$this->assertTrue($this->template->fileExists($key));
		$this->assertEquals($file, $this->template->getFile($key));

		$newValue = 'my/path';
		$this->template->addFile($key, $newValue);
		$this->assertTrue($this->template->fileExists($key));
		$this->assertEquals($newValue, $this->template->getFile($key));

		/* does not matter what the value is aslong as its valid */		
		$newValue2 = $this->getMock('Appfuel\Framework\FileInterface');
		$this->template->addFile($key, $newValue);
		$this->assertTrue($this->template->fileExists($key));
		$this->assertEquals($newValue, $this->template->getFile($key));
	}

	/**
	 * @return null
	 */
	public function testGetFilesAsStrings()
	{
		$files = array(
			'file_a' => 'file/to/some',
			'file_b' => 'file/to/someWhere'
		);
	
		$this->assertFalse($this->template->fileExists('file_a'));
		$this->assertFalse($this->template->fileExists('file_b'));
	
		$result = $this->template->getFiles();
		$this->assertInternalType('array', $result);
		$this->assertEmpty($result);

		$this->template->addFile('file_a', $files['file_a']);
		$this->template->addFile('file_b', $files['file_b']);

		$result = $this->template->getFiles();
		$this->assertInternalType('array', $result);
		$this->assertEquals($files, $result);
	}

	/**
	 * @return null
	 */
	public function testGetFilesAsFileInterfaces()
	{
		$files = array(
			'file_a' => $this->getMock('Appfuel\Framework\FileInterface'),
			'file_b' => $this->getMock('Appfuel\Framework\FileInterface')
		);
	
		$this->assertFalse($this->template->fileExists('file_a'));
		$this->assertFalse($this->template->fileExists('file_b'));
	
		$result = $this->template->getFiles();
		$this->assertInternalType('array', $result);
		$this->assertEmpty($result);

		$this->template->addFile('file_a', $files['file_a']);
		$this->template->addFile('file_b', $files['file_b']);

		$result = $this->template->getFiles();
		$this->assertInternalType('array', $result);
		$this->assertEquals($files, $result);
	}

	/**
	 * @return null
	 */
	public function testGetFilesAsMixed()
	{
		$files = array(
			'file_a' => $this->getMock('Appfuel\Framework\FileInterface'),
			'file_b' => 'file/to/someWhere'
		);
	
		$this->assertFalse($this->template->fileExists('file_a'));
		$this->assertFalse($this->template->fileExists('file_b'));
	
		$result = $this->template->getFiles();
		$this->assertInternalType('array', $result);
		$this->assertEmpty($result);

		$this->template->addFile('file_a', $files['file_a']);
		$this->template->addFile('file_b', $files['file_b']);

		$result = $this->template->getFiles();
		$this->assertInternalType('array', $result);
		$this->assertEquals($files, $result);
	}

	/**
	 * @return null
	 */
	public function testBuildFileDoesNotExist()
	{
		$this->assertFalse($this->template->fileExists('no_file'));
		$this->assertEquals('', $this->template->buildFile('no_file'));
	}
	
	/**
	 * @expectedException Appfuel\Framework\Exception
	 * @return null
	 */
	public function testBuildFilePathDoesNotExist()
	{
		$this->template->addFile('my-file', 'path/to/no/where');
		$this->template->buildFile('my-file');
	}

	/**
	 * This build is using a controlled template file in the files directory
	 * located in the current directory of this test. Because we know the 
	 * contents of the template file we can test it against the string
	 * buildFile produces
	 *
	 * @return null
	 */
	public function testBuildFilePrivateScope()
	{
		$path = 'files' . DIRECTORY_SEPARATOR . 'build_private.txt';
		$path = $this->getCurrentPath($path);
		$file = $this->getMock('Appfuel\Framework\FileInterface');
		
		$file->expects($this->any())
			 ->method('isFile')
			 ->will($this->returnValue(true));

		$file->expects($this->any())
			 ->method('getRealPath')
			 ->will($this->returnValue($path));

		$this->template->addFile('my-file', $file);

		$data = array(
			'foo' => 'bat',
			'bar' => 'bam',
			'baz' => 'boo'
		);
		$privateScope = true;
		$result = $this->template->buildFile('my-file', $data, $privateScope);
		$expected  = "Test buildFile with private scope:foo=bat and ";
		$expected .= "bar=bam and baz=boo EOF";
		$this->assertEquals($expected, $result);
	}

	/**
	 * @expectedException Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddFileBadKeyEmptyString()
	{
		$this->template->addFile('', 'somepath');
	}

	/**
	 * @expectedException Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddFileBadKeyArray()
	{
		$this->template->addFile(array(1,3,4), 'somepath');
	}

	/**
	 * @expectedException Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddFileBadKeyObject()
	{
		$this->template->addFile(new StdClass(), 'somepath');
	}

	/**
	 * @expectedException Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddFileBadKeyNull()
	{
		$this->template->addFile(null, 'somepath');
	}

	/**
	 * @expectedException Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddFileBadPathEmptyString()
	{
		$this->template->addFile('my-key', '');
	}

	/**
	 * @expectedException Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddFileBadPathNull()
	{
		$this->template->addFile('my-key', null);
	}

	/**
	 * @expectedException Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddFileBadPathInteger()
	{
		$this->template->addFile('my-key', 1234);
	}

	/**
	 * @expectedException Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddFileBadPathArray()
	{
		$this->template->addFile('my-key', array(1,3,2));
	}

	/**
	 * @expectedException Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddFileBadPathObject()
	{
		$this->template->addFile('my-key', new StdClass());
	}
}
