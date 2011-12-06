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
	SplFileInfo,
	Appfuel\View\ViewTemplate,
	TestFuel\TestCase\BaseTestCase;

/**
 * The view template is a basic template that uses a text formatter by default
 * to convert its data into a string
 */
class ViewTemplateTest extends BaseTestCase
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
		$this->template	= new ViewTemplate();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->template = null;
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
	 * @return	array
	 */
	public function provideValidTextBuild()
	{
        return array(
            array('key1', 'my string', 'key1 my string'),
            array('key2', '', 'key2'),
            array('key3', 1234, 'key3 1234'),
            array('key4', 1234.43, 'key4 1234.43'),
            array('key5', array(), 'key5'),
            array('key6', array(1,2,3), 'key6 0 1 1 2 2 3'),
            array('key7', array('a' => 'b', 'c'=> 'd'), 'key7 a b c d'),
            array('key8', new SplFileInfo('/my/path'), 'key8 /my/path'),
            array('key9', new StdClass(), 'key9')
        );
	}


	/**
	 * @return	null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\View\ViewTemplateInterface',
			$this->template
		);
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testDefaultsFromConstructor()
	{	
		$this->assertEquals(0, $this->template->count());
		$this->assertEquals(array(), $this->template->getAllAssigned());

		$formatter = $this->template->getViewFormatter();
		$this->assertInstanceOf(
			'Appfuel\View\Formatter\TextFormatter',
			$formatter
		);
	}

	/**
	 * @depends	testDefaultsFromConstructor
	 * @return	null
	 */
	public function testConstructorData()
	{
		$data = array('foo' => 'bar', 'baz' => 'biz');
		$template = new ViewTemplate($data);
		$this->assertEquals(count($data), $template->count());
		$this->assertEquals($data, $template->getAllAssigned());
	}

	/**
	 * @depends	testDefaultsFromConstructor
	 * @return	null
	 */
	public function testConstructorViewFormatter()
	{
		$interface = 'Appfuel\View\Formatter\ViewFormatterInterface';
		$formatter = $this->getMock($interface);
		$template = new ViewTemplate(null, $formatter);

		$this->assertEquals(0, $template->count());
		$this->assertEquals(array(), $template->getAllAssigned());
		$this->assertSame($formatter, $template->getViewFormatter());
	}

	/**
	 * @depends	testDefaultsFromConstructor
	 * @return	null
	 */
	public function testConstructorDataAndViewFormatter()
	{
		$interface = 'Appfuel\View\Formatter\ViewFormatterInterface';
		$formatter = $this->getMock($interface);
		$data = array('foo' => 'bar', 'baz' => 'biz');
		
		$template = new ViewTemplate($data, $formatter);

		$this->assertEquals(count($data), $template->count());
		$this->assertEquals($data, $template->getAllAssigned());
		$this->assertSame($formatter, $template->getViewFormatter());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testGetSetFormatter()
	{
		$interface = 'Appfuel\View\Formatter\ViewFormatterInterface';
		$formatter = $this->getMock($interface);
		
		$result = $this->template->getViewFormatter();
		$this->assertNotEquals($formatter, $result);

		$this->assertSame(
			$this->template,
			$this->template->setViewFormatter($formatter),
			'uses a fluent interface'
		);
		$this->assertSame($formatter, $this->template->getViewFormatter());
	}

    /**
     * @dataProvider    provideValidAssigns
     * @depends         testInterface
     * @return          null
     */
    public function testAssignGetAssignedIsAssigned($key, $value)
    {  
        $default = 'this is a default';
        $this->assertFalse($this->template->isAssigned($key));
        $this->assertNull($this->template->getAssigned($key));
        $this->assertEquals(
			$default, 
			$this->template->getAssigned($key, $default)
		);

        $this->assertSame(
            $this->template,
            $this->template->assign($key, $value),
            'uses fluent interface'
        );
        $this->assertTrue($this->template->isAssigned($key));
        $this->assertEquals($value, $this->template->getAssigned($key));

    }
	
	/**
	 * @depends	testAssignGetAssignedIsAssigned
	 * @return	null
	 */
	public function testGetAllAssignedLoadCount()
	{
        $data = array(
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
            'key4' => 'value4'
        );

        $this->assertEquals(0, $this->template->count());
        $this->assertSame(
            $this->template,
            $this->template->load($data),
            'uses fluent interface'
        );
        $this->assertEquals(count($data), $this->template->count());

        foreach ($data as $key => $value) {
            $this->assertTrue($this->template->isAssigned($key));
            $this->assertEquals($value, $this->template->getAssigned($key));
        }

        $this->assertEquals($data, $this->template->getAllAssigned());
	}

	/**
	 * assignDeep splits the key on '.' and uses each part as a template 
	 * name with the last part being the key. Since the ViewTemplate can not
	 * hold any other templates than this has no meaning the key will still
	 * be the key
	 *
	 * @depends	testInterface
	 * @return	null
	 */
	public function testAssignDeepAsNoMeaning()
	{
		$key = 'template1.template2.template3';
		$value = 'value';
		$this->assertSame(
			$this->template, 
			$this->template->assign($key, $value, true)
		);
		$this->assertEquals($value, $this->template->getAssigned($key));
	}

	/**
	 * Note that this is using the text formatter in ArrayAssoc mode which will
	 * convert both keys and values even when the array is non assoc
	 *
	 * @dataProvider	provideValidTextBuild
	 * @depends			testInterface
	 * @return			null
	 */
	public function testBuildWithTextFormatter($key, $value, $result)
	{
		$this->template->assign($key, $value);
		$this->assertEquals($result, $this->template->build());
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testBuildPrivateTextFormatter()
	{
		$data = array(
			'foo' => 'bar',
			'baz' => 'biz'
		);
		$expected = 'foo bar baz biz';
		$this->assertEquals($expected, $this->template->build($data, true));

		$formatter = $this->template->getViewFormatter();
		$formatter->setFormatArrayKeys();
		$expected = 'foo baz';
		$this->assertEquals($expected, $this->template->build($data, true));
	
		$expected = 'bar biz';
		$formatter->setFormatArrayValues();
		$this->assertEquals($expected, $this->template->build($data, true));
	}

	/**
	 * @depends	testInterface
	 * @return	null
	 */
	public function testBuildMergeTextFormatter()
	{
		$data = array(
			'baz' => 'biz',
		);
		$this->template->assign('foo', 'bar');

		$expected = 'foo bar baz biz';
		$this->assertEquals($expected, $this->template->build($data));

		$formatter = $this->template->getViewFormatter();
		$formatter->setFormatArrayKeys();
		$expected = 'foo baz';
		$this->assertEquals($expected, $this->template->build($data));
	
		$expected = 'bar biz';
		$formatter->setFormatArrayValues();
		$this->assertEquals($expected, $this->template->build($data));
	}
}
