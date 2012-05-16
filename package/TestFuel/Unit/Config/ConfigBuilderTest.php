<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Testfuel\Unit\Config;

use Appfuel\Config\ConfigBuilder,
	Testfuel\TestCase\BaseTestCase;

/**
 */
class ConfigBuilderTest extends BaseTestCase
{
	/**
	 * @var	ConfigBuilderInterface
	 */
	protected $builder = null;

	/**
	 * @var string
	 */
	protected $currentEnv = null;
	
	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->currentEnv = 'local';
		$this->builder = new ConfigBuilder($this->currentEnv);
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->builder = null;
	}

	/**
	 * @return	ConfigBuilder
	 */
	public function getBuilder()
	{
		return $this->builder;
	}

	/**
	 * @return	string
	 */
	public function getCurrentEnv()
	{
		return $this->currentEnv;
	}

	/**
	 * @return	null
	 */
	public function testInitialState()
	{
		$builder = $this->getBuilder();
		$this->assertInstanceOf(
			'Appfuel\Config\ConfigBuilderInterface', 
			$builder
		);

		$finder = $builder->getFileFinder();
		$this->assertInstanceOf(
			'Appfuel\Filesystem\FileFinderInterface',
			$finder
		);
		$this->assertEquals('app/config', $finder->getRootPath());

		$reader = $builder->getFileReader();
		$this->assertInstanceOf(
			'Appfuel\Filesystem\FileReaderInterface',
			$reader
		);
		$this->assertSame($finder, $reader->getFileFinder());

		$writer = $builder->getFileWriter();
		$this->assertInstanceOf(
			'Appfuel\Filesystem\FileWriterInterface',
			$writer
		);
		$this->assertSame($finder, $writer->getFileFinder());

	}
}
