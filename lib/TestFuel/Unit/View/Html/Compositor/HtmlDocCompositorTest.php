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
namespace TestFuel\Unit\View\Compositor;

use StdClass,
	SplFileInfo,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\View\Compositor\FileCompositor,
	Appfuel\View\Html\Compositor\HtmlDocCompositor;

/**
 */
class HtmlDocCompositorTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HtmlDocCompositor
	 */
	protected $compositor = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->compositor = new HtmlDocCompositor();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->compositor = null;
	}

	/**
	 * @return null
	 */
	public function testInterface()
	{
		$this->assertInstanceOf(
			'Appfuel\View\Compositor\ViewCompositorInterface',
			$this->compositor
		);

		$this->assertInstanceOf(
			'Appfuel\View\Html\Compositor\HtmlCompositorInterface',
			$this->compositor
		);

		$this->assertInstanceOf(
			'Appfuel\View\Html\Compositor\HtmlDocCompositorInterface',
			$this->compositor
		);
	}

	/**
	 * You can change the template file to you own implementation by 
	 * giving a relative path from the template path given in the config.
	 * 
	 * @return	null
	 */
	public function testSetFileInConstructor()
	{
		$path = 'path/to/my/template';
		$compositor = new HtmlDocCompositor($path);
		$this->assertEquals($compositor->getFile(), $path);
	}

	/**
	 * @return	null
	 */
	public function testDefaults()
	{
		$expected = 'appfuel/html/htmldoc.phtml';
		$this->assertEquals($expected, $this->compositor->getFile());
		$pathFinder = $this->compositor->getPathFinder();
		$this->assertInstanceOf('Appfuel\Kernel\PathFinder', $pathFinder);
	
		/* by default base path is enabled */	
		$this->assertTrue($pathFinder->isBasePathEnabled());

		/* by default template is what is set in configuration and if no
		 * configuration exists than it wiill be 'ui' directory
		 */
		$this->assertEquals('ui', $pathFinder->getRelativeRootPath());
	}

	/**
	 * Whatever FileCompositor::getTemplatePath is set to, the default 
	 * pathfinder will have as its RootRelativePath
	 *
	 * @return	null
	 */
	public function testDefaultsSetFileCompositorManually()
	{
		$backup = FileCompositor::getTemplatePath();
		$path = 'my/path';
		FileCompositor::setTemplatePath($path);
		
		$compositor = new HtmlDocCompositor();
		$pathFinder = $compositor->getPathFinder();
		$this->assertEquals($path, $pathFinder->getRelativeRootPath());
		FileCompositor::setTemplatePath($backup);
	}

	public function testConstructorSetPathFinder()
	{
		$finder = $this->getMock('Appfuel\Kernel\PathFinderInterface');
		$compositor = new HtmlDocCompositor(null, $finder);
		$this->assertSame($finder, $compositor->getPathFinder());
	}

	/**
	 * @return	array
	 */
	public function provideDocTypeKeys()
	{
		return array(
			array(''),
			array('html5'),
			array('html401-strict'),
			array('html401-transitional'),
			array('html401-frameset'),
			array('xhtml10-strict'),
			array('xhtml10-transitional'),
			array('xhtml10-frameset'),
			array('xhtml11'),
			array('xhtml11-basic'),
			array('mathml20'),
			array('mathml101'),
			array('xhtml+mathml+svg'),
			array('xhtml-host+mathml+svg'),
			array('xhtml+mathml+svg-host'),
			array('svg11-full'),
			array('svg10'),
			array('svg11-basic'),
			array('svg11-tiny')
		);
	}

	/**
	 * Values used to show only a strict bool false can toggle the switch,
	 * all other values will result in true
	 *
	 * @return	array
	 */
	public function provideDefaultToTrue()
	{
		return array(
			array(false,	false),
			array(true,		true),
			array(null,		true),
			array('',		true),
			array(array(),	true),
			array(array(1),	true),
			array('false',	true),
			array(0,		true),
			array(1,		true),
			array(new StdClass(), true),
			array(-1,		true),
			array('true',	true)
		);	
	}

	public function testGetDefaultCharset()
	{
		$expected = '<meta http-equiv="Content-Type" ' . 
					'content="text/html; charset=utf-8">';
		$this->assertEquals($expected, $this->compositor->getDefaultCharset());
	}

	/**
	 * @dataProvider	provideDefaultToTrue
	 * @depends			testInterface
	 * @return			null
	 */
	public function testIsCssEnabled($value, $expected)
	{
		$this->compositor->assign('is-css-enabled', $value);
		$this->assertEquals($expected, $this->compositor->isCssEnabled());
	}

	/**
	 * @dataProvider	provideDefaultToTrue
	 * @depends			testInterface
	 * @return			null
	 */
	public function testIsInlineCssEnabled($value, $expected)
	{
		$this->compositor->assign('is-inlinecss-enabled', $value);
		$this->assertEquals($expected, $this->compositor->isInlineCssEnabled());
	}

	/**
	 * @dataProvider	provideDefaultToTrue
	 * @depends			testInterface
	 * @return			null
	 */
	public function testIsJsEnabled($value, $expected)
	{
		$this->compositor->assign('is-js-enabled', $value);
		$this->assertEquals($expected, $this->compositor->isJsEnabled());
	}

	/**
	 * @dataProvider	provideDefaultToTrue
	 * @depends			testInterface
	 * @return			null
	 */
	public function testIsJsHeadInlineEnabled($value, $expected)
	{
		$this->compositor->assign('is-jsinline-head-enabled', $value);
		$this->assertEquals(
			$expected, 
			$this->compositor->isJsHeadInlineEnabled()
		);
	}

	/**
	 * @dataProvider	provideDefaultToTrue
	 * @depends			testInterface
	 * @return			null
	 */
	public function testIsJsBodyInlineEnabled($value, $expected)
	{
		$this->compositor->assign('is-jsinline-body-enabled', $value);
		$this->assertEquals(
			$expected, 
			$this->compositor->isJsBodyInlineEnabled()
		);
	}


}
