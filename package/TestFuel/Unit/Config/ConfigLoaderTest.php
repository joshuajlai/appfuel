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

use StdClass,
	Testfuel\TestCase\BaseTestCase,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileReader,
	Appfuel\Config\ConfigLoader,
	Appfuel\Config\ConfigRegistry;

/**
 */
class ConfigLoaderTest extends BaseTestCase
{
    /**
	 * Backup the configuration registry
     * @var array
     */
    protected $backup = array();

	/**
	 * Path to file used to test loading of configuration
	 * @var string
	 */
	protected $phpFilename = 'test-config.php';

	/**
	 * @var string
	 */
	protected $jsonFilename = 'test-config.json';

    /**
     * @return  null
     */
    public function setUp()
    {
        $this->backup = ConfigRegistry::getAll();
        ConfigRegistry::clear();
    }

    /**
     * @return  null
     */
    public function tearDown()
    {
        ConfigRegistry::setAll($this->backup);
    }

	/**
	 * @return	string
	 */
	public function getConfigPHPFilename()
	{
		return $this->phpFilename;
	}

	/**
	 * @return	string
	 */
	public function getConfigJsonFilename()
	{
		return $this->jsonFilename;
	}

	/**
	 * @test
	 * @return	ConfigLoader
	 */
	public function createConfigLoaderDefaultReader()
	{
		$loader = new ConfigLoader();
		$this->assertInstanceOf(
			'Appfuel\Config\ConfigLoaderInterface',
			$loader
		);

		return $loader;
	}

	/**
	 * @test
	 * @return	ConfigLoader
	 */
	public function createConfigLoaderWithReader()
	{
		$reader = new FileReader(new FileFinder('test/files/config')); 
		$loader = new ConfigLoader($reader);
		$this->assertInstanceOf(
			'Appfuel\Config\ConfigLoaderInterface',
			$loader
		);

		return $loader;
	}

	/**
	 * @test
	 * @depends	createConfigLoaderDefaultReader
	 * @return	ConfigLoader
	 */
	public function getReaderWhenCreatedWithNoParams(ConfigLoader $loader)
	{
		$reader = $loader->getFileReader();
		$this->assertInstanceof(
			'Appfuel\Filesystem\FileReaderInterface',
			$reader
		);

		$finder = $reader->getFileFinder();
		$this->assertTrue($finder->isBasePath());
		$this->assertEquals('', $finder->getRootPath());

		return $loader;
	}

	/**
	 * @test
	 * @depends	createConfigLoaderWithReader
	 * @return	ConfigLoader
	 */
	public function getReaderWithTestReader(ConfigLoader $loader)
	{
		$reader = $loader->getFileReader();
		$this->assertInstanceof(
			'Appfuel\Filesystem\FileReaderInterface',
			$reader
		);
		$finder = $reader->getFileFinder();
		$this->assertTrue($finder->isBasePath());
		$this->assertEquals('test/files/config', $finder->getRootPath());
	
		return $loader;
	}

	/**
	 * @test
	 * @depends	getReaderWithTestReader
	 * @return	ConfigLoader
	 */
	public function getFileDataPHP(ConfigLoader $loader)
	{
		$file = $this->getConfigPHPFilename();
		$data = $loader->getFileData($file);
		$this->assertInternalType('array', $data);
		$this->assertArrayHasKey('common', $data);
		$this->assertArrayHasKey('section-a', $data);
		$this->assertArrayHasKey('section-b', $data);

		return $loader;
	}

	/**
	 * @test
	 * @depends	getReaderWithTestReader
	 * @return	ConfigLoader
	 */
	public function getFileDataPHPFileDoesNotExist(ConfigLoader $loader)
	{
		$this->setExpectedException('RunTimeException');
		$file = 'file-will-not-be-found.php';
		$data = $loader->getFileData($file);

		return $loader;
	}

	/**
	 * @test
	 * @depends	getReaderWithTestReader
	 * @return	ConfigLoader
	 */
	public function getFileDataJson(ConfigLoader $loader)
	{
		$file = $this->getConfigJsonFilename();
		$data = $loader->getFileData($file);
		
		$this->assertInternalType('array', $data);
		$this->assertArrayHasKey('common', $data);
		$this->assertArrayHasKey('section-a', $data);
		$this->assertArrayHasKey('section-b', $data);
	}

	/**
	 * @test
	 * @depends	getReaderWithTestReader
	 * @return	ConfigLoader
	 */
	public function getFileDataJsonFileDoesNotExist(ConfigLoader $loader)
	{
		$this->setExpectedException('RunTimeException');
		$file = 'file-will-not-be-found.json';
		$data = $loader->getFileData($file);

		return $loader;
	}

	/**
	 * Load does not clear any items, it only appends to the config
	 * @test
	 * @depends	createConfigLoaderDefaultReader
	 * @return	null
	 */
	public function loadDataIntoTheRegistry(ConfigLoader $loader)
	{
		$data = array(
			'key-a' => 'value-a',
			'key-b' => 'value-b'
		);
		$this->assertFalse(ConfigRegistry::exists('key-a'));
		$this->assertFalse(ConfigRegistry::exists('key-b'));

		$this->assertNull($loader->load($data));
		$this->assertTrue(ConfigRegistry::exists('key-a'));
		$this->assertTrue(ConfigRegistry::exists('key-b'));

		$this->assertFalse(ConfigRegistry::exists('key-c'));
		$more = array('key-c' => 'value-c');
		$this->assertNull($loader->load($more));
		$this->assertTrue(ConfigRegistry::exists('key-a'));
		$this->assertTrue(ConfigRegistry::exists('key-b'));
		$this->assertTrue(ConfigRegistry::exists('key-c'));
	}

	/**
	 * Set clears all items before is loads
	 *
	 * @test
	 * @depends	createConfigLoaderDefaultReader
	 * @return	null
	 */
	public function setDataIntoTheRegistry(ConfigLoader $loader)
	{
		$data = array(
			'key-a' => 'value-a',
			'key-b' => 'value-b'
		);
		$this->assertFalse(ConfigRegistry::exists('key-a'));
		$this->assertFalse(ConfigRegistry::exists('key-b'));

		$this->assertNull($loader->set($data));
		$this->assertTrue(ConfigRegistry::exists('key-a'));
		$this->assertTrue(ConfigRegistry::exists('key-b'));

		$this->assertFalse(ConfigRegistry::exists('key-c'));
		$more = array('key-c' => 'value-c');
		$this->assertNull($loader->set($more));
		$this->assertFalse(ConfigRegistry::exists('key-a'));
		$this->assertFalse(ConfigRegistry::exists('key-b'));
		$this->assertTrue(ConfigRegistry::exists('key-c'));
	}
}
