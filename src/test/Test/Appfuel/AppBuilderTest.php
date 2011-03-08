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
namespace Test\Appfuel\Stdlib\Autoload;

use Test\AfCase;
use Appfuel\AppBuilder;

/**
 * 
 */
class AppBuilderTest extends AfCase
{
	/**
	 * System Under Test
	 * @var Appfuel\AppBuilder
	 */
	protected $builder = NULL;

	/**
	 * Root path of the application
	 * @var string
	 */
	protected $basePath = NULL;

	/**
	 * @return null
	 */
	public function setUp()
	{
		$this->basePath = TEST_AF_BASE_PATH;
		$this->builder = new AppBuilder($this->basePath);
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		unset($this->builder);
	}

    /**
     * The constructor sets the base path as an immutable member
     * @return void
     */
    public function testConstructor()
    {
		$this->assertEquals(
			$this->basePath,
			$this->builder->getBasePath()
		);
    }

	/**
	 * The path to appfuel's config file is used when no other config file 
	 * is given.
	 */
	public function testDefaultConfigFile()
	{	
		$expected = $this->basePath . DIRECTORY_SEPARATOR .
					'config' . DIRECTORY_SEPARATOR . 'app.ini';

		$this->assertEquals(
			$expected,
			$this->builder->getDefaultConfigPath()
		);
	}

	public function testInit()
	{
		$result = $this->builder->init();
		echo "\n", print_r('insert here',1), "\n";exit;			
	}

}

