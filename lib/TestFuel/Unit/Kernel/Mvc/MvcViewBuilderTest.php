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
namespace TestFuel\Unit\Kernel\Mvc;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Kernel\Mvc\MvcViewBuilder;

/**
 * Responsible for create console, ajax, and general view templates. Also 
 * builds and configures html pages
 */
class MvcViewBuilderTest extends BaseTestCase
{
	/**
	 * System under test
	 * @var MvcViewBuilder
	 */
	protected $factory = null;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->builder = new MvcViewBuilder();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->builder = null;
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$this->assertInstanceOf(
			'Appfuel\Kernel\Mvc\MvcViewBuilderInterface',
			$this->builder
		);

        $this->assertInstanceOf(
            'Appfuel\ClassLoader\StandardAutoLoader',
            $this->builder->getClassLoader()
        );
	
		$this->assertInstanceOf(
			'Appfuel\View\Html\HtmlPageBuilder',
			$this->builder->getHtmlPageBuilder()
		);
	}

    /**
     * @depends testInitialState
     * @return  null
     */
    public function testSetClassLoader()
    {
        $loader = $this->getMock('Appfuel\ClassLoader\AutoLoaderInterface');
        $this->assertNotSame($loader, $this->builder->getClassLoader());

        $this->assertSame(
            $this->builder,
            $this->builder->setClassLoader($loader)
        );

        $this->assertSame($loader, $this->builder->getClassLoader());
		
		$builder = new MvcViewBuilder($loader);
        $this->assertSame($loader, $builder->getClassLoader());
    }

    /**
     * @depends testInitialState
     * @return  null
     */
    public function testSetHtmlPageBuilder()
    {
        $pageB = $this->getMock('Appfuel\View\Html\HtmlPageBuilderInterface');
        $this->assertNotSame($pageB, $this->builder->getHtmlPageBuilder());

        $this->assertSame(
            $this->builder,
            $this->builder->setHtmlPageBuilder($pageB)
        );

        $this->assertSame($pageB, $this->builder->getHtmlPageBuilder());

		$builder = new MvcViewBuilder(null, $pageB);
		$this->assertSame($pageB, $builder->getHtmlPageBuilder());
    }

	/**
	 * @return	null
	 */
	public function testConstructorBothParams()
	{
        $loader = $this->getMock('Appfuel\ClassLoader\AutoLoaderInterface');
        $pageB = $this->getMock('Appfuel\View\Html\HtmlPageBuilderInterface');

		$builder = new MvcViewBuilder($loader, $pageB);
        $this->assertSame($loader, $builder->getClassLoader());
		$this->assertSame($pageB, $builder->getHtmlPageBuilder());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testCreateDefaultConsoleTemplate()
	{
		$result = $this->builder->createDefaultConsoleTemplate();
		$this->assertInstanceOf('Appfuel\Console\ConsoleTemplate', $result);
	}
}
