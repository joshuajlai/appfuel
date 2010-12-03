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
use Appfuel\StdLib\Config\Factory					as ConfigFactory;
use Appfuel\StdLib\Config\Adapter\AdapterInterface	as AdapterInterface;
use Appfuel\StdLib\Filesystem\File					as File;

/**
 * Autoloader
 *
 * @package 	Appfuel
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Test	createAdapter
	 * @return	NULL
	 */
	public function testCreateAdapter()
	{
		$this->assertType(
			'Appfuel\StdLib\Config\Adapter\AdapterInterface',
			ConfigFactory::createAdapter('ini')
		);
	}

	/**
	 * Test	createAdapter
	 * Exception is thrown from the autoloader because it tries to find
	 * the class
	 *
	 * @expectedException	\Appfuel\StdLib\Config\Exception
	 * @return	NULL
	 */
	public function testCreateAdapterNoAdapter()
	{
		ConfigFactory::createAdapter('noClass');
	}

	/**
	 * Test createFile
	 *
	 * @return	NULL
	 */
	public function testCreateFile()
	{
		$this->assertType(
			'Appfuel\StdLib\Filesystem\File',
			ConfigFactory::createFile('some\path')
		);
	}

}

