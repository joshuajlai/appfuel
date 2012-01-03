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
namespace TestFuel\Unit\View\Html;

use StdClass,
	SplFileInfo,
	Appfuel\View\ViewTemplate,
	Appfuel\View\Html\HtmlPage,
	Appfuel\View\Html\Tag\HtmlTag,
	TestFuel\TestCase\BaseTestCase;

/**
 */
class HtmlPageTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HtmlPage
	 */
	protected $page = null;

	/**
	 * Content view of the html page. This gets turned into a string and
	 * added as the first content item of the body
	 *
	 * @var	 ViewTemplate
	 */
	protected $view = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->view = new ViewTemplate();
		$this->view->getViewCompositor()
					->setFormatArrayValues();
		$this->view->assign('foo', '<h1>this is a title</title>');
		$this->page = new HtmlPage($this->view);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->page = null;
	}

	/**
	 * @return null
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\View\Html\HtmlPageInterface',
			$this->page
		);
		$this->assertSame($this->view, $this->page->getView());
		$script = $this->page->getInlineJs();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\ScriptTag',
			$script
		);
		$this->assertTrue($script->isEmpty());

		$html = $this->page->getHtmlTag();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\HtmlTagInterface',
			$html
		);
	}
}
