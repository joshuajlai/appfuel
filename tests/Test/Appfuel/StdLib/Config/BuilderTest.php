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
use Appfuel\StdLib\Filesystem\Manager		as FileManager;
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
	 * The config file we are using in this test has four sections
	 * production, qa, dev, local. Production has the following keys
	 * label_1, label_2, label_3. All the other sections contain at least
	 * one of these keys with different values.
	 *
	 * @return	NULL
	 */
	public function testBuildWithInheritence()
	{
		$file = new File($this->configPath);

		/* 
		 * parse the config ini to test against
		 * check for the inherit section and the 
		 * current section
		 */
		$config = FileManager::parseIni($file, TRUE);
		$this->assertInternalType('array', $config);
		$this->assertArrayHasKey('production', $config);
		$this->assertArrayHasKey('dev', $config);

		$list = $this->builder
					 ->enableInheritance()
					 ->inherit('production')
					 ->setSection('dev')
					 ->build($file);

		$type = '\Appfuel\StdLib\Ds\AfList\Basic';
		$this->assertType($type, $list);
		
		/* because of inheritance all the labels from
		 * production will be availiable
		 */
		$this->assertTrue($list->isKey('label_1'));
		$this->assertTrue($list->isKey('label_2'));
		$this->assertTrue($list->isKey('label_3'));

		/* inherited value*/
		$prod = $config['production'];
		$dev  = $config['dev'];
		$this->assertEquals($prod['label_1'], $list->get('label_1'));
		$this->assertEquals($prod['label_2'], $list->get('label_2'));
		$this->assertEquals($dev['label_3'], $list->get('label_3'));
	}

	/**
	 * @return NULL
	 */
	public function testBuildNoInheritance()
	{
		$file = new File($this->configPath);

		/* 
		 * parse the config ini to test against
		 * check for the inherit section and the 
		 * current section
		 */
		$config = FileManager::parseIni($file, TRUE);
		$this->assertInternalType('array', $config);
		$this->assertArrayHasKey('dev', $config);

		$list = $this->builder
					 ->disableInheritance()
					 ->setSection('dev')
					 ->build($file);

		$type = '\Appfuel\StdLib\Ds\AfList\Basic';
		$this->assertType($type, $list);
		$this->assertEquals(1, $list->count());
		$this->assertEquals('value_c', $list->get('label_3'));	
	}

	/**
	 * The first check in build is for the file adapter which
	 * is based on the method call setFileStrategy. The name
	 * given is check against that class name in the adapter dir
	 * when nothing is found an exception is thrown. 
	 *
	 * @expectedException	\Appfuel\StdLib\Config\Exception
	 */
	public function testBuildNoFileAdapter()
	{
		$file = new File($this->configPath);
		$list = $this->builder
					 ->setFileStrategy('doesNotExist')
					 ->setSection('dev')
					 ->build($file);

	}



	/**
	 * The second check in build is for the section. If the section
	 * you want in the file is not there an exception is thrown
	 *
	 * @expectedException	\Appfuel\StdLib\Config\Exception
	 */
	public function testBuildNoSection()
	{
		$file = new File($this->configPath);
		$list = $this->builder
					 ->build($file);

	}

	/**
	 * The third check happens when inheritance is enabled and the section
	 * you want to inherit from is not available. 
	 *
	 * @expectedException	\Appfuel\StdLib\Config\Exception
	 */
	public function testBuildNoInheritSection()
	{
		$file = new File($this->configPath);
		$list = $this->builder
					 ->enableInheritance()
					 ->inherit('doesNotExist')
					 ->setSection('dev')
					 ->build($file);

	}

	/**
	 *
	 * @expectedException	\Appfuel\StdLib\Filesystem\Exception
	 */
	public function testBuildFileDoesNotExist()
	{
		$file = new File('/path/to/nowhere');
		$list = $this->builder
					 ->disableInheritance()
					 ->setSection('dev')
					 ->build($file);

	
	}

}

