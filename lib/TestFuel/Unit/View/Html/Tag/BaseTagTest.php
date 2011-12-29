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
namespace TestFuel\Unit\View\Html\Element;

use StdClass,
	SplFileInfo,
	Appfuel\View\Html\Tag\BaseTag,
	TestFuel\TestCase\BaseTestCase;

/**
 * Test the base tag
 */
class BaseTagTest extends BaseTestCase
{
    /**
     * System under test
     * @var Base
     */
    protected $base = null;

	/**
	 * Passed into the constructor href attribute value
	 * @var string
	 */
	protected $href = null;

	/**
	 * Passed into the constructor taget attribute value
	 * @var string
	 */
	protected $target = null;

    /**
     * @return null
     */
    public function setUp()
    {   
		$this->href   = 'http://www.someurl.com/css/';
		$this->target = '_blank';
        $this->base   = new BaseTag($this->href, $this->target);
    }

    /**
     * @return null
     */
    public function tearDown()
    {   
        $this->base = null;
    }

	/**
	 * @return null
	 */
	public function testConstructor()
	{
		$this->assertInstanceOf(
			'\Appfuel\View\Html\Tag\GenericTagInterface',
			$this->base,
			'must extend the tag class'
		);

		/*
		 * no content should be added to this tag  
		 */
		$expected = array();
		$this->assertEquals($expected, $this->base->getContent());
	}

	/**
	 * @return null
	 */
	public function testBuild()
	{
		$expected = '<base href="' . $this->href   . 
					'" target="'   . $this->target . '"/>';
	
		$this->assertEquals($expected, $this->base->build()); 

		$base = new BaseTag($this->href);

		$expected = '<base href="' . $this->href . '"/>';
		$this->assertEquals($expected, $base->build());


		$base = new BaseTag(null, $this->target);
		$expected = '<base target="' . $this->target . '"/>';
		
		$this->assertEquals($expected, $base->build()); 
	}
}
