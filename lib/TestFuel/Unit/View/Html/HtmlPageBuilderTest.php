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
	Appfuel\View\Html\HtmlPageDetail,
	Appfuel\View\Html\HtmlPageBuilder,
	TestFuel\Functional\View\Html\ExtendedLayoutTemplate;

/**
 * The action factory is reponsible for creating any of the objects in the
 * action controller namespace
 */
class HtmlPageBuilderTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var HtmlPageBuilder
	 */
	protected $builder = null;

	/**
	 * Used to create mock objects
	 * @var string
	 */
	protected $detailInterface = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->detailInterface = 'Appfuel\View\Html\HtmlPageDetailInterface';
		$this->builder = new HtmlPageBuilder();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->builder = null;
	}

	/**
	 * @return	HtmlPageDetailInterface
	 */
	public function getMockPageDetail()
	{
		return $this->getMock($this->detailInterface);
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\View\Html\HtmlPageBuilderInterface',
			$this->builder
		);

		$finder = $this->builder->getPathFinder();
		$this->assertInstanceOf(
			'Appfuel\Kernel\PathFinder',
			$finder
		);

		$this->assertEquals('ui', $finder->getRelativeRootPath());

		$config = $this->builder->getPageConfiguration();
		$this->assertInstanceOf(
			'Appfuel\View\Html\HtmlPageConfiguration',
			$config
		);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetPathFinder()
	{
		$finder = $this->getMock('Appfuel\Kernel\PathFinderInterface');
		$this->assertNotSame($finder, $this->builder->getPathFinder());
		
		$builder = new HtmlPageBuilder($finder);
		$this->assertSame($finder, $builder->getPathFinder());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testSetHtmlPageConfiguration()
	{
		$interface = 'Appfuel\View\Html\HtmlPageConfigurationInterface';
		$config = $this->getMock($interface);
	
		$builder = new HtmlPageBuilder(null, $config);
	
		$finder = $builder->getPathFinder();	
		$this->assertInstanceOf('Appfuel\Kernel\PathFinder',$finder);
		
		$this->assertSame($config, $builder->getPageConfiguration());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateTagFactoryTagFactoryObject()
	{
		$finterface = 'Appfuel\View\Html\Tag\HtmlTagFactoryInterface';
		$factory = $this->getMock($finterface);

		$detail = $this->getMockPageDetail();
		$detail->expects($this->once())
			   ->method('getTagFactory')
			   ->will($this->returnValue($factory));

		$result = $this->builder->createTagFactory($detail);
		$this->assertSame($factory, $result);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateTagFactoryClassThatExists()
	{
		$class = 'TestFuel\Functional\View\Html\Tag\ExtendedTagFactory';

		$detail = $this->getMockPageDetail();
		$detail->expects($this->once())
			   ->method('getTagFactory')
			   ->will($this->returnValue($class));

		$result = $this->builder->createTagFactory($detail);
		$this->assertInstanceOf($class, $result);
	}

	/**
	 * @expectedException	RunTimeException
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testCreateTagFactoryClassNotFound()
	{
		$detail = $this->getMockPageDetail();
		$detail->expects($this->once())
			   ->method('getTagFactory')
			   ->will($this->returnValue(new StdClass()));

		$this->builder->createTagFactory($detail);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateViewTemplatePageDetailUsesAString()
	{
		$tpl = 'some/tpl/file.phtml';
		$detail = $this->getMockPageDetail();
		$detail->expects($this->once())
			   ->method('getViewTemplate')
			   ->will($this->returnValue($tpl));

		$result = $this->builder->createViewTemplate($detail);
		$this->assertInstanceOf('Appfuel\View\FileViewTemplate', $result);
		$this->assertEquals($tpl, $result->getFile());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateViewTemplatePageDetailUsesAnObject()
	{
		$view = $this->getMock('Appfuel\View\FileViewInterface');
		$detail = $this->getMockPageDetail();
		$detail->expects($this->once())
			   ->method('getViewTemplate')
			   ->will($this->returnValue($view));

		$result = $this->builder->createViewTemplate($detail);
		$this->assertSame($view, $result);
	}

	/**
	 * @expectedException	RunTimeException
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testCreateViewTemplatePageDetailUsesWrongInterface()
	{
		$detail = $this->getMockPageDetail();
		$detail->expects($this->once())
			   ->method('getViewTemplate')
			   ->will($this->returnValue(new StdClass()));

		$this->builder->createViewTemplate($detail);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateLayoutTemplateDetailUsesString()
	{
		$view = $this->getMock('Appfuel\View\ViewInterface');

		$class  = 'TestFuel\Functional\View\Html\ExtendedLayoutTemplate';
		$detail = $this->getMockPageDetail();
		$detail->expects($this->once())
			   ->method('getLayoutTemplate')
			   ->will($this->returnValue($class));

	
		$result = $this->builder->createLayoutTemplate($detail, $view);
		$this->assertInstanceOf($class, $result);
		$this->assertSame($view, $result->getView());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateLayoutTemplateDetailUsesLayoutObject()
	{
		$view = $this->getMock('Appfuel\View\ViewInterface');

		$layout  = new ExtendedLayoutTemplate();
		$detail = $this->getMockPageDetail();
		$detail->expects($this->once())
			   ->method('getLayoutTemplate')
			   ->will($this->returnValue($layout));

	
		$this->assertFalse($layout->isViewTemplate());
		$result = $this->builder->createLayoutTemplate($detail, $view);
		$this->assertSame($layout, $result);
		$this->assertTrue($layout->isViewTemplate());
		$this->assertSame($view, $layout->getView());
	}

	/**
	 * @expectedException	RunTimeException
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testCreateLayoutTemplateDetailUsesLayoutWrongInterface()
	{
		$view = $this->getMock('Appfuel\View\ViewInterface');

		$detail = $this->getMockPageDetail();
		$detail->expects($this->once())
			   ->method('getLayoutTemplate')
			   ->will($this->returnValue(new StdClass()));

		$this->builder->createLayoutTemplate($detail, $view);
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateHtmlPageDetailUsesDefaultPage()
	{
		$view = $this->getMock('Appfuel\View\ViewInterface');
		$tagFactory = null;

		$detail = $this->getMockPageDetail();
		$detail->expects($this->once())
			   ->method('isHtmlPageClass')
			   ->will($this->returnValue(false));


		$result = $this->builder->createHtmlPage($detail, $view, $tagFactory);
		$this->assertInstanceOf('Appfuel\View\Html\HtmlPage', $result);
		$this->assertSame($view, $result->getView());

		$tagFactory = $result->getTagFactory();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\HtmlTagFactory',
			$tagFactory
		);
	}
	
	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateHtmlPageDetailUsesCustomPage()
	{
		$view = $this->getMock('Appfuel\View\ViewInterface');
		$tagFactory = null;

		$pageClass = 'TestFuel\Functional\View\Html\ExtendedPage';
		$detail = $this->getMockPageDetail();
		$detail->expects($this->once())
			   ->method('isHtmlPageClass')
			   ->will($this->returnValue(true));

		$detail->expects($this->once())
			   ->method('getHtmlPageClass')
			   ->will($this->returnValue($pageClass));

		$result = $this->builder->createHtmlPage($detail, $view, $tagFactory);
		$this->assertInstanceOf($pageClass, $result);
		$this->assertSame($view, $result->getView());

		$tagFactory = $result->getTagFactory();
		$this->assertInstanceOf(
			'Appfuel\View\Html\Tag\HtmlTagFactory',
			$tagFactory
		);
	}

	/**
	 * @expectedException	RunTimeException
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testCreateHtmlPageDetailUsesCustomPageWrongInterface()
	{
		$view = $this->getMock('Appfuel\View\ViewInterface');
		$tagFactory = null;

		$pageClass = 'StdClass';
		$detail = $this->getMockPageDetail();
		$detail->expects($this->once())
			   ->method('isHtmlPageClass')
			   ->will($this->returnValue(true));

		$detail->expects($this->once())
			   ->method('getHtmlPageClass')
			   ->will($this->returnValue($pageClass));

		$result = $this->builder->createHtmlPage($detail, $view, $tagFactory);
	}

	/**
	 * @dataProvider		provideInvalidStrings
	 * @expectedException	InvalidArgumentException
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testGetConfigurationDataNotString_Failure($str)
	{
		$this->builder->getConfigurationData($str);
	}
	
	/**
	 * @expectedException	InvalidArgumentException
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testGetConfigurationDataEmptyString_Failure()
	{
		$this->builder->getConfigurationData('');
	}
}
