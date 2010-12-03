<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @category 	Tests
 * @package 	Appfuel
 * @author 		Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright	2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace Test\Appfuel\StdLib\Config;

/* import */
use Appfuel\StdLib\Config\Builder			as ConfigBuilder;
use Appfuel\StdLib\Filesystem\File			as File;

/**
 * Autoloader
 *
 * @package 	Appfuel
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * System under test
	 * @return	ConfigBuilder
	 */ 	
	protected $builder = NULL;

	/**
	 * @return	NULL
	 */
	public function setUp()
	{
		$this->builder = new ConfigBuilder();
		$this->configPath  = AF_TEST_EXAMPLE_PATH . DIRECTORY_SEPARATOR . 
							 'config'			  . DIRECTORY_SEPARATOR .
							 'config.ini';
	}

	/**
	 * @return	NULL
	 */
	public function	tearDown()
	{
		unset($this->builder);
	}

	/**
	 * Test	inherit, getInheritSection
	 * @return	NULL
	 */
	public function testInherit()
	{
		/* prove default value for inherited section */
		$this->assertEquals('production', $this->builder->getInheritSection());

		/* prove fluent interface and inherit setter */
		$section = 'my_section';
		$this->assertSame($this->builder,$this->builder->inherit($section));
		$this->assertEquals($section, $this->builder->getInheritSection());
	}

	
	/**
	 * Test isInheritence, enableInheritence, disableInheritence, 
	 *		setInheritenceFlag
	 *
	 * enable/disable methods use setInheritanceFlag so it is indirectly tested
	 * @return	NULL
	 */
	public function testIsInheritance()
	{
		/* prove default value for flag */
		$this->assertTrue($this->builder->isInheritance());

		/* prove fluent interface and setter */
		$this->assertSame(
			$this->builder, 
			$this->builder->disableInheritance()
		);
		$this->assertFalse($this->builder->isInheritance());

		/* prove fluent interface and setter */
		$this->assertSame(
			$this->builder, 
			$this->builder->enableInheritance()
		);
		$this->assertTrue($this->builder->isInheritance());
	}

	/**
	 * Test isDatatypeHint, enableDatatypeHint, disableDatatypeHint
	 * @return	NULL
	 */
	public function testIsDatatypeHint()
	{
		/* prove default value for flag */
		$this->assertTrue($this->builder->isDatatypeHint());

		/* prove fluent interface and setter */
		$this->assertSame(
			$this->builder, 
			$this->builder->disableDatatypeHint()
		);
		$this->assertFalse($this->builder->isDatatypeHint());

		/* prove fluent interface and setter */
		$this->assertSame(
			$this->builder, 
			$this->builder->enableDatatypeHint()
		);
		$this->assertTrue($this->builder->isDatatypeHint());
	}

	/**
	 * Test getFileStrategy, setFileStategy
	 * Testing strategies with adapters known to exist. 
	 * @return	NULL
	 */
	public function testFileStrategy()
	{
		/* prove default file strategy */
		$this->assertEquals('ini', $this->builder->getFileStrategy());

		/* prove fluent interface and setter */
		$this->assertSame(
			$this->builder, 
			$this->builder->setFileStrategy('xml')
		);
		$this->assertEquals('xml', $this->builder->getFileStrategy());
	}

	/**
	 * Test setFileStategy
	 * @expectedException \Exception
	 * @return	NULL
	 */
	public function testFileStrategyNoStrategy()
	{
		/* prove fluent interface and setter */
		$this->assertSame(
			$this->builder, 
			$this->builder->setFileStrategy('blah')
		);
	}


	/**
	 * Test getFileAdapter, setFileAdapter
	 * @return	NULL
	 */
	public function testFileAdapter()
	{
		/* prove default file adapter */
		$type    = '\Appfuel\StdLib\Config\Adapter\AdapterInterface';
		$default = $this->builder->getFileAdapter();
		$this->assertType($type,$default);

		$adapter = $this->getMock($type);
		/* prove fluent interface and setter */
		$this->assertSame(
			$this->builder, 
			$this->builder->setFileAdapter($adapter)
		);
		$this->assertSame($adapter, $this->builder->getFileAdapter());
		$this->assertNotSame($default, $this->builder->getFileAdapter());
	}

	/**
	 * Test getFilePath, setFilePath
	 * @return	NULL
	 */
	public function testFilePath()
	{
		/* prove default file path */
		$this->assertNull($this->builder->getFilePath());

		/* prove fluent interface and setter */
		$path = '/some/path';
		$this->assertSame(
			$this->builder, 
			$this->builder->setFilePath($path)
		);
		$this->assertEquals($path, $this->builder->getFilePath());
	}



	/**
	 * @return	NULL
	 */
	public function testBuild()
	{
		$file = new File($this->configPath);
		$result = $this->builder->build($file);
		echo "\n", print_r($file->isFile(),1), "\n";exit; 
	}
}

