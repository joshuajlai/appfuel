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
namespace TestFuel\Test\View\Html\Element;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Html\Element\Tag,
	Appfuel\View\Html\Element\Title;

/**
 * The html element tag is used to automate the rendering of the html element
 * and provide a simpler interface to add data to the element
 */
class TitleTest extends BaseTestCase
{
    /**
     * System under test
     * @var Message
     */
    protected $title = null;

	/**
	 * Passed into the constructor to be added to the title content
	 * @var string
	 */
	protected $content = null;

	/**
	 * Second parameter used in the constructor for content separator
	 * @var string
	 */
	protected $separator = null;

    /**
     * @return null
     */
    public function setUp()
    {   
		$this->content   = 'This is a title';
		$this->separator = ':';
        $this->title = new Title($this->content, $this->separator);
    }

    /**
     * @return null
     */
    public function tearDown()
    {   
        unset($this->title);
    }

	/**
	 * @return null
	 */
	public function testConstructor()
	{
		$this->assertInstanceOf(
			'\Appfuel\View\Html\Element\Tag',
			$this->title,
			'must extend the tag class'
		);

		/*
		 * constructor should have added the content because it was a string 
		 */
		$expected = array($this->content);
		$this->assertEquals($expected, $this->title->getContent());
		$this->assertEquals($this->separator, $this->title->getSeparator());
	
		/* no content is allowed */	
		$title = new Title();
		$this->assertEquals(array(), $title->getContent());
		$this->assertEquals(' ', $title->getSeparator());

		/* emoty contented is tolerated but not suggested */
		$title = new Title('');
		$this->assertEquals(array(), $title->getContent());

		/* arrays are ignored */	
		$title = new Title(array(1,2,3), array(1,2,3));
		$this->assertEquals(array(), $title->getContent());
		$this->assertEquals(' ', $title->getSeparator());


		/* objects are ignored */
		$title = new Title(new StdClass(), new StdClass());
		$this->assertEquals(array(), $title->getContent());
		$this->assertEquals(' ', $title->getSeparator());
				
	}

	/**
	 * @return null
	 */
	public function testBuild()
	{
		$expected = "<title>{$this->content}</title>";
		$this->assertEquals($expected, $this->title->build());

		$this->title->addContent('more data');
		$content = $this->content . $this->separator . 'more data';
		$expected = "<title>{$content}</title>";
		$this->assertEquals($expected, $this->title->build());
	}
}
