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
namespace TestFuel\Test\View\Formatter;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Formatter\TemplateFormatter;

/**
 * The template formatter converts a php template into a string. It binds 
 * itself with the template file by doing an include call which makes the 
 * parser class the $this reference in the template file. All functions except
 * format and include support the functionality of the template file
 */
class TemplateFormatterTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var TextFormatter
	 */
	protected $formatter = null;

    /**
     * Path to template file 
     * @var string
     */
    protected $templatePath = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$path = "{$this->getTestFilesPath()}/ui/appfuel/template.phtml";	
		$this->templatePath = $path;

		$this->formatter = new TemplateFormatter($this->templatePath);
	}

    /**
     * @return  string
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->formatter = null;
	}

	/**
	 * Provides valid data for testing assignments
	 * 
	 * @return array
	 */
	public function provideValidAssigns()
	{
		return array(
			array('string', 'this is a string value'),
			array('empty-string', ''),
			array('number', 12345),
			array('float', 1.234),
			array('object', new StdClass()),
			array('array', array(1,2,3)),
			array('empty-array', array()),
			array('null', null),
			array('true', true),
			array('false', false),
			array(0, 'numbered key 0'),
			array(5, 'numbered key 5'),
		);
	}

	/**
	 * Provides valid data for testing the render method
	 *
	 * @return	array
	 */
	public function provideValidRender()
	{
		return array(
			array('key1', 'my string', 'my string'),
			array('key2', '', ''),
			array('key3', 1234, '1234'),
			array('key4', 1234.43, '1234.43'),
			array('key5', array(), ''),
			array('key6', array(1,2,3), '1 2 3'),
			array('key7', array('a' => 'b', 'c'=> 'd'), 'b d'),
			array('key8', new SplFileInfo('/my/path'), '/my/path'),
			array('key9', new StdClass(), '')
		);
	}

	/**
	 * Provides valid data for testing the render method
	 *
	 * @return	array
	 */
	public function provideValidRenderNotFound()
	{
		return array(
			array('my string', 'my string'),
			array('', ''),
			array(1234, '1234'),
			array(1234.43, '1234.43'),
			array(array(), ''),
			array(array(1,2,3), '1 2 3'),
			array(array('a' => 'b', 'c'=> 'd'), 'b d'),
			array(new SplFileInfo('/my/path'), '/my/path'),
			array(new StdClass(), '')
		);
	}

	/**
	 * Provides known data for rendering arrays with a given seperator
	 * 
	 * @return	array
	 */
	public function provideRenderArraySeparator()
	{
		$data = array(1,2,3,4);
		$assoc = array('a'=>'b', 'c' => 'd');
		return array(
			array('key1', $data, ' ', '1 2 3 4'),
			array('key2', $data, ':', '1:2:3:4'),
			array('key3', $data, '', '1234'),
			array('key4', $data, '--', '1--2--3--4'),
			array('key5', $assoc, ' ', 'b d'),
			array('key6', $assoc, ':', 'b:d'),
			array('key7', $assoc, '', 'bd'),
			array('key8', $assoc, '--', 'b--d'),	
		);
	}

	/**
	 * Provide known data and results for render json output
	 * 
	 * @return	array
	 */
	public function provideRenderJson()
	{
		$obj = new StdClass();
        $obj->firstName = 'first name';
        $obj->roles     = array('role1','role2','role3');
        $obj->object    = new StdClass();
        $obj->int       = 12345;
        $obj->string    = 'i am a string';

		$expected = '{"firstName":"first name",' .
					'"roles":["role1","role2","role3"],' .
                    '"object":{},' .
                    '"int":12345,' .
                    '"string":"i am a string"}';
		return array(
			array('key1', '', '""'),
			array('key2', 'this is a string', '"this is a string"'),
			array('key3', 0, "0"),
			array('key4', -1234, "-1234"),
			array('key5', 1234, "1234"),
			array('key6', 123456789123456789, "123456789123456789"),
			array('key7', 1234.123, "1234.123"),
			array('key8', array(), '[]'),
			array('key9', array(1,2,3,4), '[1,2,3,4]'),
			array('key10', array('a' => 'b'),'{"a":"b"}'),
			array('key11', array('a' => array(1,2,3)), '{"a":[1,2,3]}'),
			array('key12', new StdClass(), '{}'),
			array('key13', $obj, $expected),
		);
	} 

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\Framework\View\Formatter\ViewFormatterInterface',
			$this->formatter
		);
	}

    /**
     * When no data is given in the constructor then the count is 0 and 
     * getAll will return an empty array. Basically no data will be in scope
     *
     * @return null
     */
    public function testConstructorNoData()
    {
        $this->assertEquals(0, $this->formatter->count());

        $result = $this->formatter->getAll();
        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
    }

	/**
	 * @dataProvider	provideValidAssigns
	 * @depends			testInterface
	 * @return			null
	 */
	public function testAssignExistsGet($key, $value)
	{
		$default = 'this is a default';
		$this->assertFalse($this->formatter->exists($key));
		$this->assertNull($this->formatter->get($key));
		$this->assertEquals($default, $this->formatter->get($key, $default));

		$this->assertSame(
			$this->formatter, 
			$this->formatter->assign($key, $value),
			'uses fluent interface'
		);
		$this->assertTrue($this->formatter->exists($key));
		$this->assertEquals($value, $this->formatter->get($key));
		
	}

	/**
	 * @depends	testConstructorNoData
	 * @return	null
	 */
    public function testAssignCount()
    {
		$key1   = 'string';
		$value1 = 'this is a string';
		$key2   = 'number';
		$value2 = 12345; 

		$this->assertEquals(0, $this->formatter->count());	
		$this->formatter->assign($key1, $value1);
		$this->assertEquals(1, $this->formatter->count());
			
		$this->formatter->assign($key2, $value2);
		$this->assertEquals(2, $this->formatter->count());
    }

	/**
	 * When 'get' can not find the key the developer can supply thier own
	 * defaults to return. We will test those defaults here.
	 *
	 * @depends	testAssignCount
	 * @return	null
	 */
	public function testGetDefaultValue()
	{	
		$key = 'key-not-found';
		$this->assertFalse($this->formatter->exists($key));
		
		/* the default return value when a key is not found */
		$this->assertNull($this->formatter->get($key));

		$default = 'this is a string';
		$this->assertEquals($default, $this->formatter->get($key, $default));

		$default = false;
		$this->assertEquals($default, $this->formatter->get($key, $default));

		$default = true;
		$this->assertEquals($default, $this->formatter->get($key, $default));

		$default = array(1,2,3);
		$this->assertEquals($default, $this->formatter->get($key, $default));

		$default = new StdClass();
		$this->assertEquals($default, $this->formatter->get($key, $default));
	}

	/**
	 * @depends	testAssignExistsGet
	 * @return	null
	 */
	public function testLoadGetAll()
	{
		$data = array(
			'key1' => 'value1',
			'key2' => 'value2',
			'key3' => 'value3',
			'key4' => 'value4'
		);

		$this->assertEquals(0, $this->formatter->count());
		$this->assertSame(
			$this->formatter,
			$this->formatter->load($data),
			'uses fluent interface'
		);
		$this->assertEquals(count($data), $this->formatter->count());

		foreach ($data as $key => $value) {
			$this->assertTrue($this->formatter->exists($key));
			$this->assertEquals($value, $this->formatter->get($key));
		}

		$this->assertEquals($data, $this->formatter->getAll());	
	}

	/**
	 * @dataProvider	provideValidRender
	 * @return			null
	 */
	public function testRender($key, $value, $result)
	{
		$this->formatter->assign($key, $value);
		$this->expectOutputString($result);
		$this->formatter->render($key);
	}

	/**
	 * @dataProvider	provideRenderArraySeparator
	 * @return			null
	 */
	public function testRenderArrayWithSeparator($key, $value, $sep, $result)
	{
		$this->formatter->assign($key, $value);
		$this->expectOutputString($result);
		$this->formatter->render($key, null, $sep);

	}

	/**
	 * @dataProvider	provideValidRenderNotFound
	 * @return			null
	 */
	public function testRenderNotFound($default, $result)
	{
		$this->expectOutputString($result);
		$this->formatter->render('not-found', $default);
	}

	/**
	 * In this test the array value is passed into the default param and 
	 * because the key will never be found we will test the array parse
	 * in the not found if statements
	 *
	 * @dataProvider	provideRenderArraySeparator
	 * @return			null
	 */
	public function testRenderNotFoundArrayWithSep($key, $value, $sep, $result)
	{
		$this->expectOutputString($result);
		$this->formatter->render('not found', $value, $sep);

	}

	/**
	 * @dataProvider	provideRenderJson
	 * @return			null
	 */
	public function testRenderJson($key, $value, $result)
	{
		$this->formatter->assign($key, $value);
		$this->expectOutputString($result);
		$this->formatter->renderAsJson($key);

	}

	/**
	 * @dataProvider	provideRenderJson
	 * @return			null
	 */
	public function testRenderJsonNotFound($key, $value, $result)
	{
		$this->expectOutputString($result);
		$this->formatter->renderAsJson('not-found', $value);

	}


    /**
     * Using a simple template file that uses a single variable
     * in a line of text we will build it into a string giving
     * just the path to the template as a string
     *
     * @return null
     */
    public function testFormatString()
    {
        $data = array(
            'foo' => 'bar'
        );

        $result = $this->formatter->format($data);
        $expected = 'This is a test template. Foo=bar. EOF.';
        $this->assertEquals($expected, $result);
    }


    /**
     * Same test as above accept with an SplFileInfo object data is loaded
	 * into the formatter
     *
     * @return null
     */
    public function testFormatWithInConstructorFileObjectLoadData()
    {
        $file = new SplFileInfo($this->getTemplatePath());
        $data = array(
            'foo' => 'bar'
        );

        $formatter = new TemplateFormatter($file, $data);
        $result = $formatter->format(null);

        $expected = 'This is a test template. Foo=bar. EOF.';
        $this->assertEquals($expected, $result);
    }

	/**
     * Same test as above accept with an SplFileInfo object
     * and data is passed into format
	 *
     * @return null
     */
    public function testFormatWithInConstructorFileObject()
    {
        $file = new SplFileInfo($this->getTemplatePath());
        $data = array(
            'foo' => 'bar'
        );

        $formatter = new TemplateFormatter($file);
        $result = $formatter->format($data);

        $expected = 'This is a test template. Foo=bar. EOF.';
        $this->assertEquals($expected, $result);
    }

	/**
	 * In this test we will not pass any data and the template will render
	 * with default data
	 *
     * @return null
     */
    public function testFormatWithDefaultData()
    {
		$this->assertEquals(0, $this->formatter->count());
        $result = $this->formatter->format(null);

        $expected = 'This is a test template. Foo=baz. EOF.';
        $this->assertEquals($expected, $result);
    }

    /**
     * Show that with the absolute path you can build a file from within
     * a template file
     *
     * @return null
     */
    public function testFormatFileBuildInTemplate()
    {
        $path = "{$this->getTestFilesPath()}/ui/appfuel/include_template.phtml";
        $file = new SplFileInfo($path);
        $data = array('template_path' => $this->getTemplatePath());
        $formatter = new TemplateFormatter($file);
        $result = $formatter->format($data);
        $expected = 'New template: This is a test template. Foo=baz. EOF.';
        $this->assertEquals($expected, $result);
    }
}
