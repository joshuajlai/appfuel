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
namespace TestFuel\Unit\App\Resource;

use Appfuel\Kernel\PathFinder,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\App\Resource\Yui3DependencyResolver;

/**
 * This is a file list that only allows css files to be added
 */
class Yui3DependencyResolverTest extends BaseTestCase
{
	/**
	 * System Under Test
	 * @var Yui3DependencyResolver
	 */
	protected $resolver = null;

	/**
	 * Relative path to the dependency file for yui3
	 * @var string
	 */
	protected $file = null;

	/**
	 * Used to resolve relative file paths
	 * @var PathFinder
	 */
	protected $finder = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->file = 'src/loader/js/yui3.json';
		$this->finder = new PathFinder('ui/yui3');
		$this->resolver = new Yui3DependencyResolver(
			$this->file, 
			$this->finder
		);
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->list = null;
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$this->assertEquals('min', $this->resolver->getMode());
	}

	/**
	 * @depends	testInitialState
	 * @return	null
	 */
	public function testMode()
	{
		$this->assertSame($this->resolver, $this->resolver->setMode('raw'));
		$this->assertEquals('raw', $this->resolver->getMode());
	
		$this->assertSame($this->resolver, $this->resolver->setMode('debug'));
		$this->assertEquals('debug', $this->resolver->getMode());
		
		$this->assertSame($this->resolver, $this->resolver->setMode('min'));
		$this->assertEquals('min', $this->resolver->getMode());
	}

	/**
	 * Any empty string defaults to min
	 * 
	 * @dataProvider	provideEmptyStrings
	 * @depends			testInitialState
	 * @return		null
	 */
	public function testModeEmptyString($mode)
	{
		$this->assertSame($this->resolver, $this->resolver->setMode($mode));
		$this->assertEquals('min', $this->resolver->getMode());
	}

	/**
	 * @dataProvider		provideInvalidStrings
	 * @depends				testInitialState
	 * @return				null
	 */
	public function testModeInvalidString_Failure($mode)
	{
		$this->assertSame($this->resolver, $this->resolver->setMode($mode));
		$this->assertEquals('min', $this->resolver->getMode());
	}

	public function testGetModules()
	{
		$this->resolver->resolve(array('widget'));
	}
}
