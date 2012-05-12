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
namespace TestFuel\Unit\View;

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
			'Appfuel\View\ViewInterface',
			$this->template
		);
	}
}
