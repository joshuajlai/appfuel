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
	TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Html\HtmlPageDetail;

/**
 * The page detail is a value object used to describe the html document that
 * appfuel needs to build.
 */
class HtmlPageDetailTest extends BaseTestCase
{
	/**
	 * It is possible to have an empty page detail
	 * @return	null
	 */
	public function testEmptyDetail()
	{
		$detail = new HtmlPageDetail(array());
		
		$this->assertInstanceOf(
			'Appfuel\View\Html\HtmlPageDetailInterface',
			$detail
		);

		$this->assertFalse($detail->isHtmlConfig());
		$this->assertNull($detail->getHtmlConfig());
		
		$this->assertFalse($detail->isHtmlDoc());
		$this->assertNull($detail->getHtmlDoc());
		
		$this->assertFalse($detail->isHtmlPageClass());
		$this->assertNull($detail->getHtmlPageClass());

		$this->assertFalse($detail->isTagFactory());
		$this->assertNull($detail->getTagFactory());
		
		$this->assertFalse($detail->isLayoutTemplate());
		$this->assertNull($detail->getLayoutTemplate());
		
		$this->assertFalse($detail->isInlineJsTemplate());
		$this->assertNull($detail->getInlineJsTemplate());

		$this->assertFalse($detail->isViewTemplate());
		$this->assertNull($detail->getViewTemplate());
	}

	/**
	 * @depends			testEmptyDetail
	 * @dataProvider	provideNonEmptyStrings
	 * @return			null
	 */
	public function testSetPageClass($class)
	{
		$data = array('html-page-class' => $class);
		$detail = new HtmlPageDetail($data);
		$this->assertTrue($detail->isHtmlPageClass());
		$this->assertEquals($class, $detail->getHtmlPageClass());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testEmptyDetail
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testSetPageClassInvalidString_Failure($class)
	{
		$data = array('html-page-class' => $class);
		$detail = new HtmlPageDetail($data);
	}

	/**
	 * @depends			testEmptyDetail
	 * @dataProvider	provideNonEmptyStrings
	 * @return			null
	 */
	public function testSetHtmlDoc($doc)
	{
		$data = array('html-doc' => $doc);
		$detail = new HtmlPageDetail($data);
		$this->assertTrue($detail->isHtmlDoc());
		$this->assertEquals($doc, $detail->getHtmlDoc());
	}

	/**
	 * @depends			testEmptyDetail
	 * @return			null
	 */
	public function testSetHtmlDocViewTemplateInterface()
	{
		$doc = $this->getMock('Appfuel\View\ViewInterface');
		
		$data = array('html-doc' => $doc);
		$detail = new HtmlPageDetail($data);
		$this->assertTrue($detail->isHtmlDoc());
		$this->assertSame($doc, $detail->getHtmlDoc());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testEmptyDetail
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testSetHtmlDocPathInvalidString_Failure($doc)
	{
		$data = array('html-doc' => $doc);
		$detail = new HtmlPageDetail($data);
	}

	/**
	 * @depends			testEmptyDetail
	 * @dataProvider	provideNonEmptyStrings
	 * @return			null
	 */
	public function testSetHtmlConfig($path)
	{
		$data = array('html-config' => $path);
		$detail = new HtmlPageDetail($data);
		$this->assertTrue($detail->isHtmlConfig());
		$this->assertEquals($path, $detail->getHtmlConfig());
	}

	/**
	 * @depends			testEmptyDetail
	 * @return			null
	 */
	public function testSetHtmlConfigWithArray()
	{
		$data = array('html-config' => array('opt1' => 'value1'));
		$detail = new HtmlPageDetail($data);
		$this->assertTrue($detail->isHtmlConfig());
		$this->assertEquals($data['html-config'], $detail->getHtmlConfig());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testEmptyDetail
	 * @return				null
	 */
	public function testSetHtmlConfigInvalidString_Failure()
	{
		$data = array('html-config' => new StdClass());
		$detail = new HtmlPageDetail($data);
	}

	/**
	 * @depends			testEmptyDetail
	 * @dataProvider	provideNonEmptyStrings
	 * @return			null
	 */
	public function testSetTagFactory($class)
	{
		$data = array('tag-factory' => $class);
		$detail = new HtmlPageDetail($data);
		$this->assertTrue($detail->isTagFactory());
		$this->assertEquals($class, $detail->getTagFactory());
	}

	/**
	 * @depends			testEmptyDetail
	 * @return			null
	 */
	public function testSetTagFactoryInterface()
	{
		$obj = $this->getMock('Appfuel\View\Html\Tag\HtmlTagFactoryInterface');
		$data = array('tag-factory' => $obj);
		$detail = new HtmlPageDetail($data);
		$this->assertTrue($detail->isTagFactory());
		$this->assertEquals($obj, $detail->getTagFactory());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testEmptyDetail
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testSetTagFactoryInvalidString_Failure($class)
	{
		$data = array('tag-factory' => $class);
		$detail = new HtmlPageDetail($data);
	}

	/**
	 * @depends			testEmptyDetail
	 * @dataProvider	provideNonEmptyStrings
	 * @return			null
	 */
	public function testSetInlineJsString($js)
	{
		$data = array('inline-js-template' => $js);
		$detail = new HtmlPageDetail($data);
		$this->assertTrue($detail->isInlineJsTemplate());
		$this->assertEquals($js, $detail->getInlineJsTemplate());
	}

	/**
	 * @depends			testEmptyDetail
	 * @return			null
	 */
	public function testSetInlineJsTemplateTemplateViewTemplateInterface()
	{
		$view = $this->getMock('Appfuel\View\ViewInterface');
		
		$data = array('inline-js-template' => $view);
		$detail = new HtmlPageDetail($data);
		$this->assertTrue($detail->isInlineJsTemplate());
		$this->assertEquals($view, $detail->getInlineJsTemplate());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testEmptyDetail
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testSetInlineJsTemplateInvalidString_Failure($path)
	{
		$data = array('inline-js-template' => $path);
		$detail = new HtmlPageDetail($data);
	}

	/**
	 * @depends			testEmptyDetail
	 * @dataProvider	provideNonEmptyStrings
	 * @return			null
	 */
	public function testSetLayout($class)
	{
		$data = array('layout-template' => $class);
		$detail = new HtmlPageDetail($data);
		$this->assertTrue($detail->isLayoutTemplate());
		$this->assertEquals($class, $detail->getLayoutTemplate());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testEmptyDetail
	 * @dataProvider		provideInvalidStrings
	 * @return				null
	 */
	public function testSetLayoutInvalidString_Failure($class)
	{
		$data = array('layout-template' => $class);
		$detail = new HtmlPageDetail($data);
	}

	/**
	 * @depends			testEmptyDetail
	 * @dataProvider	provideNonEmptyStrings
	 * @return			null
	 */
	public function testSetViewTemplateString($view)
	{
		$data = array('view-template' => $view);
		$detail = new HtmlPageDetail($data);
		$this->assertTrue($detail->isViewTemplate());
		$this->assertEquals($view, $detail->getViewTemplate());
	}

	/**
	 * @depends			testEmptyDetail
	 * @return			null
	 */
	public function testSetViewTemplateViewTemplateInterface()
	{
		$view = $this->getMock('Appfuel\View\ViewInterface');
		
		$data = array('view-template' => $view);
		$detail = new HtmlPageDetail($data);
		$this->assertTrue($detail->isViewTemplate());
		$this->assertEquals($view, $detail->getViewTemplate());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @dataProvider		provideInvalidStrings
	 * @depends				testEmptyDetail
	 * @return				null
	 */
	public function testSetViewTemplateInvalidString_Failure($path)
	{
		$data = array('view-template' => $path);
		$detail = new HtmlPageDetail($data);
	}

	/**
	 * @depends				testEmptyDetail
	 * @return	null
	 */
	public function testAll()
	{
		$data = array(
			'html-page-class'		=> 'my-class',
			'html-doc'				=> 'my/path.phtml',
			'html-config'			=> 'my/config.php',
			'tag-factory'			=> 'my_tag_factory',
			'layout-template'		=> 'my_layout',
			'inline-js-template'	=> 'my_js_template_class',
			'view-template'			=> 'my_view_class'
		);

		$detail = new HtmlPageDetail($data);
		$this->assertEquals('my-class', $detail->getHtmlPageClass());
		$this->assertEquals('my/path.phtml', $detail->getHtmlDoc());
		$this->assertEquals('my/config.php', $detail->getHtmlConfig());
		$this->assertEquals('my_tag_factory', $detail->getTagFactory());
		$this->assertTrue($detail->isLayoutTemplate());
		$this->assertEquals('my_layout', $detail->getLayoutTemplate());
		$this->assertTrue($detail->isViewTemplate());
		$this->assertEquals('my_view_class', $detail->getViewTemplate());
	}
}
