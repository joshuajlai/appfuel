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
namespace TestFuel\Test\View;

use StdClass,
	Appfuel\View\Template,
	TestFuel\TestCase\BaseTestCase;

/**
 * The view template is an extension of view data that adds on the ability 
 * to have template files the may or may not get the data in the templates
 * dictionary.
 */
class TemplateTest extends BaseTestCase
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
		$this->template	= new Template();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->template);
	}

	/**
	 * setFile can accept a string or FileInterface this tests the string
	 *
	 * @return null
	 */
	public function testSetGetFileAsString()
	{
		$file = 'path/to/some/where';
		
		$this->assertFalse($this->template->fileExists());
		
		/* note that the file does not have to exist at this point */
		$this->assertSame(
			$this->template,
			$this->template->setFile($file),
			'must use a fluent interface'
		);
		$this->assertTrue($this->template->fileExists());
		
		$result = $this->template->getFile();	
		$this->assertInstanceOf('Appfuel\View\ViewFile', $result);
		$this->assertContains($file, $result->getFullPath());
	}

	/**
	 * The second data type setFile accepts is a FileInterface.
	 *
	 * @return null
	 */
	public function testSetGetFileAsFileInterfaceCreateViewFile()
	{
		$path = 'path/to/some/where';
		$file = $this->createMockFrameworkFile($path);

		$this->assertFalse($this->template->fileExists());
		
		$this->assertSame(
			$this->template,
			$this->template->setFile($file),
			'must use a fluent interface'
		);
		$this->assertTrue($this->template->fileExists());
		$this->assertEquals($file, $this->template->getFile());

		$this->assertInstanceOf(
			'Appfuel\View\ViewFile',
			$this->template->createViewFile($path)
		);
	}

	/**
	 * The file can also be set by passing the fist parameter in the 
	 * constructor
	 *
	 * @return null
	 */
	public function testGetFileAsFileInterfaceConstructor()
	{
		$path     = 'path/to/some/where';
		$file     = $this->createMockFrameworkFile($path);
		$template = new Template($file); 
		$this->assertTrue($template->fileExists());
		$this->assertEquals($file, $template->getFile());	
	}

	/**
	 * @return null
	 */
	public function testGetFileAsStringConstructor()
	{
		$file     = 'path/to/some/where';
		$template = new Template($file); 
		$this->assertTrue($template->fileExists());
		
		$result = $template->getFile();
		$this->assertInstanceOf(
			'Appfuel\View\ViewFile',
			$result,
			'constructor used setFile which will create a viewFileByDefault'
		);
		$this->assertContains($file, $result->getFullPath());
	}

	/**
	 * Scope is used to bind the actual file (usually a .phtml file located
	 * in the clientside directory) with that data being assigned.
	 *
	 * @return null
	 */
	public function testGetSetCreateScope()
	{
		/* when nothing is passed into the constructor this is null */
		$this->assertNull($this->template->getScope());
		
		$scope = $this->getMock('Appfuel\Framework\View\ScopeInterface');
		$this->assertSame(
			$this->template,
			$this->template->setScope($scope),
			'must be a fluent interface'
		);
		$this->assertEquals($scope, $this->template->getScope());

		$this->assertInstanceOf(
			'Appfuel\Framework\View\ScopeInterface',
			$this->template->createScope()
		);

		$data = array('name'=>'value');
		$this->assertInstanceOf(
			'Appfuel\Framework\View\ScopeInterface',
			$this->template->createScope($data)
		);	
	}

	/**
	 * @return null
	 */
	public function testGetScopeConstructor()
	{
		$scope    = $this->getMock('Appfuel\Framework\View\ScopeInterface');
		$template = new Template(null, $scope); 
		$this->assertEquals($scope, $template->getScope());

		$data = array('name' => 'value');
		$template = new Template(null, $data);
		
		$scope = $template->getScope();
		$this->assertInstanceOf(
			'Appfuel\Framework\View\ScopeInterface',
			$scope
		); 
		$this->assertEquals($data, $scope->getAll());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testBuildFilePathEmpty()
	{
		$this->assertFalse($this->template->fileExists());
		$this->assertEquals('', $this->template->build());
	}

	/**
	 * @expectedException	Appfuel\Framework\Exception
	 * @return null
	 */
	public function testBuildFilePathEmptyHasBuildParamaters()
	{
		$this->assertFalse($this->template->fileExists());
		
		$data = array('name' => 'value');
		$this->assertEquals('', $this->template->build($data, true));
	}
	
	/**
	 * @expectedException Appfuel\Framework\Exception
	 * @return null
	 */
	public function testBuildFilePathDoesNotExist()
	{
		$this->template->setFile('path/to/no/where');
		$this->template->build();
	}

	/**
	 * @expectedException Appfuel\Framework\Exception
	 * @return null
	 */
	public function testBuildFilePathDoesNotExistHasBuildParamaters()
	{
		$this->template->setFile('path/to/no/where');
		$this->template->build(array('name' => 'value'), true);
	}

	/**
	 * We are testing that the build can turn a file string into a file
	 * object and use that to find the template to build. Because of this we
	 * have to use a known template in the clientside directory. So I selected
	 * the html/doc/standard.phtml which is the html document.
	 *
	 * @return null
	 */
	public function testBuildNoData()
	{
		$path = 'html/doc/standard.phtml';

		$this->template->setFile($path);
		$result = $this->template->build();
		
		$doctype   = '<!DOCTYPE HTML>';
		$openHtml  = '<html>';
		$closeHtml = '</html>';
		$openHead  = '<head>';
		$closeHead = '</head>';
		$openBody  = '<body>';
		$closeBody = '</body>';

		$this->assertContains($doctype, $result);
		$this->assertContains($openHtml, $result);
		$this->assertContains($closeHtml, $result);
		$this->assertContains($openHead, $result);
		$this->assertContains($closeHead, $result);
		$this->assertContains($openBody, $result);
		$this->assertContains($closeBody, $result);
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
		$path = '/ui/appfuel/build_file_test.txt';
		$file = $this->createMockFrameworkFile($path);
		$this->template->setFile($file);

		$data = array(
			'foo' => 'bat',
			'bar' => 'bam',
			'baz' => 'boo'
		);
		$privateScope = true;
		$result = $this->template->build($data, $privateScope);
		$expected  = "Test buildFile with private scope:foo=bat and ";
		$expected .= "bar=bam and baz=boo EOF";
		$this->assertEquals($expected, $result);
	}

	/**
	 * The template is using no default values so when using private scope
	 * when the scope is empty the param will be null resulting in an empty
	 * string in their place
	 *
	 * @return null
	 */
	public function testBuildFilePrivateScopeNoVars()
	{
		$path = '/ui/appfuel/build_file_test.txt';
		$file = $this->createMockFrameworkFile($path);

		$this->template->setFile($file);

		$data = array();
		$privateScope = true;
		$result = $this->template->build($data, $privateScope);

		$expected  = "Test buildFile with private scope:foo= and ";
		$expected .= "bar= and baz= EOF";
		$this->assertEquals($expected, $result);
	}

	/**
	 * When private scope is true and the only data is in the dictionary the
	 * template will not see those variable because it will only see data
	 * passed into the buildFile function itself
	 *
	 * @return null
	 */
	public function testBuildFilePrivateScopeDataInDictionary()
	{
		$path = '/ui/appfuel/build_file_test.txt';
		$file = $this->createMockFrameworkFile($path);
		$this->template->setFile($file);

		$data = array(
			'foo' => 'bat',
			'bar' => 'bam',
			'baz' => 'boo'
		);
		$this->template->load($data);
	
		$data = array();	
		$privateScope = true;
		$result = $this->template->build($data, $privateScope);
		$expected  = "Test buildFile with private scope:foo= and ";
		$expected .= "bar= and baz= EOF";
		$this->assertEquals($expected, $result);
	}

	/**
	 * The default second parameter for private scope is false, meaning any
	 * data in the template dictionary will be visable to the template file.
	 * The default first parameter for scope data is array(). So for this test
	 * the scope is not private and no extra data is given so only the 
	 * templates dictionary is visable to the template. For this test all
	 * variables will be in the dictionary
	 *
	 * @return null
	 */
	public function testBuildFileDefaultScopeParameterNoAdditionalScope()
	{
		$path = '/ui/appfuel/build_file_test.txt';
		$file = $this->createMockFrameworkFile($path);
		$this->template->setFile($file);

		$data = array(
			'foo' => 'bat',
			'bar' => 'bam',
			'baz' => 'boo'
		);
		$this->template->load($data);
		
		$result = $this->template->build();
		$expected  = "Test buildFile with private scope:foo=bat and ";
		$expected .= "bar=bam and baz=boo EOF";
		$this->assertEquals($expected, $result);
	}

	/**
	 * Same as above accept only some of the variable will be in scope
	 *
	 * @return null
	 */
	public function testBuildFileTemplateScopeMissingParams()
	{
		$path = '/ui/appfuel/build_file_test.txt';
		$file = $this->createMockFrameworkFile($path);
		$this->template->setFile($file);

		$data = array(
			'foo' => 'bat',
		);
		$this->template->load($data);
		
		$result = $this->template->build();
		$expected  = "Test buildFile with private scope:foo=bat and ";
		$expected .= "bar= and baz= EOF";
		$this->assertEquals($expected, $result);
	}

	/**
	 * Same as above but now we will fill in the missing params via
	 * the second argument which always you to extend the template scope
	 * with the data in that parameter
	 *
	 * @return null
	 */
	public function testBuildFileMergeParamsWithTemplate()
	{
		$path = '/ui/appfuel/build_file_test.txt';
		$file = $this->createMockFrameworkFile($path);
		$this->template->setFile($file);

		$data = array(
			'foo' => 'bat',
		);

		$this->template->load($data);
			
		$extend = array(
			'bar' => 'bam',
			'baz' => 'boo'
		);

		$result = $this->template->build($extend);
		$expected  = "Test buildFile with private scope:foo=bat and ";
		$expected .= "bar=bam and baz=boo EOF";
		$this->assertEquals($expected, $result);
	}

	/**
	 * @expectedException Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetFileBadPathEmptyString()
	{
		$this->template->setFile('');
	}

	/**
	 * @expectedException Appfuel\Framework\Exception
	 * @return null
	 */
	public function testSetFileBadPathNull()
	{
		$this->template->setFile(null);
	}

	/**
	 * @expectedException Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddFileBadPathInteger()
	{
		$this->template->setFile(1234);
	}

	/**
	 * @expectedException Appfuel\Framework\Exception
	 * @return null
	 */
	public function testAddFileBadPathArray()
	{
		$this->template->setFile(array(1,3,2));
	}

	/**
	 * @expectedException Appfuel\Framework\Exception
	 * @return null
	 */
	public function ztestAddFileBadPathObject()
	{
		$this->template->setFile(new StdClass());
	}
}
