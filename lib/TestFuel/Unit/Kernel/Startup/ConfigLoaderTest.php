<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace TestFuel\Unit\Kernel\Startup;

use StdClass,
	TestFuel\TestCase\BaseTestCase,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileReader,
	Appfuel\Kernel\ConfigLoader,
	Appfuel\Kernel\ConfigRegistry;

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
			'Appfuel\Kernel\ConfigLoaderInterface',
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
			'Appfuel\Kernel\ConfigLoaderInterface',
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
		$this->assertEquals('app/config', $finder->getRootPath());

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

	/**
	 * @test
	 * @depends	createConfigLoaderDefaultReader
	 * @return	null
	 */
	public function getSectionNoCommon(ConfigLoader $loader)
	{
		$data = array(
			'my-section' => array(
				'key-a' => 'value-a',
				'key-b' => 'value-b'
			),
			'your-section' => array(
				'key-x' => 12345
			)
		);

		$result = $loader->getSection($data, 'my-section');
		$this->assertEquals($data['my-section'], $result);
	}

	/**
	 * @test
	 * @depends	createConfigLoaderDefaultReader
	 * @return	null
	 */
	public function getSectionNothingInCommon(ConfigLoader $loader)
	{
		$data = array(
			'common' => array(
				'key-z'  => 'value-z',
				'key-xx' => 12345
			),
			'my-section' => array(
				'key-a' => 'value-a',
				'key-b' => 'value-b'
			),
			'your-section' => array(
				'key-x' => 12345
			)
		);

		$result = $loader->getSection($data, 'my-section');

		$expected = $data['my-section'];
		$expected['key-z']  = 'value-z';
		$expected['key-xx'] = 12345;

		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @depends	createConfigLoaderDefaultReader
	 * @return	null
	 */
	public function getSectionCommonScalar(ConfigLoader $loader)
	{
		$data = array(
			'common' => array(
				'key-a'  => 'common-a',
				'key-xx' => 12345
			),
			'my-section' => array(
				'key-a' => 'value-a',
				'key-b' => 'value-b'
			),
			'your-section' => array(
				'key-x' => 12345
			)
		);

		$result = $loader->getSection($data, 'my-section');

		$expected = $data['my-section'];
		$expected['key-xx'] = 12345;

		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @depends	createConfigLoaderDefaultReader
	 * @return	null
	 */
	public function getSectionCommonAssocArray(ConfigLoader $loader)
	{
		$data = array(
			'common' => array(
				'key-a'  => array(
					'sub-a' => 'a',
					'sub-b' => 'b'
				),
				'key-xx' => 12345
			),
			'my-section' => array(
				'key-a' => array(
					'sub-c' => 321
				),
				'key-b' => 'value-b'
			),
			'your-section' => array(
				'key-x' => 12345
			)
		);

		$result = $loader->getSection($data, 'my-section');
		
		$expected = $data['my-section'];
		$expected['key-xx'] = 12345;
		$expected['key-a'] = array(
			'sub-a' => 'a',
			'sub-b' => 'b',
			'sub-c' => 321
		);

		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @depends	createConfigLoaderDefaultReader
	 * @return	null
	 */
	public function getSectionNoSectionExistsFailure(ConfigLoader $loader)
	{
		$data = array(
			'my-section' => array(
				'key-b' => 'value-b'
			),
			'your-section' => array(
				'key-x' => 12345
			)
		);

		$this->setExpectedException('DomainException');
		$result = $loader->getSection($data, 'no-section');
		
	}

	/**
	 * @test
	 * @depends	createConfigLoaderDefaultReader
	 * @return	null
	 */
	public function getSectionEmptyNameFailure(ConfigLoader $loader)
	{
		$data = array(
			'my-section' => array(
				'key-b' => 'value-b'
			),
			'your-section' => array(
				'key-x' => 12345
			)
		);

		$this->setExpectedException('InvalidArgumentException');
		$result = $loader->getSection($data, '');
	}

	/**
	 * @test
	 * @depends	createConfigLoaderDefaultReader
	 * @return	null
	 */
	public function getSectionNameNotAStringFailure(ConfigLoader $loader)
	{
		$data = array(
			'my-section' => array(
				'key-b' => 'value-b'
			),
			'your-section' => array(
				'key-x' => 12345
			)
		);

		$this->setExpectedException('InvalidArgumentException');
		$result = $loader->getSection($data, 12345);
	}

	/**
	 * @test
	 * @depends	getReaderWithTestReader
	 * @return	ConfigLoader
	 */
	public function loadFileDataNoSectionReplaceTrue(ConfigLoader $loader)
	{
		$file = $this->getConfigPHPFilename();
		$this->assertNull($loader->loadFile($file));

		$data = $loader->getFileData($file);
		$this->assertEquals($data, ConfigRegistry::getAll());
		return $loader;
	}

	/**
	 * @test
	 * @depends	getReaderWithTestReader
	 * @return	ConfigLoader
	 */
	public function loadFileDataSectionReplaceTrue(ConfigLoader $loader)
	{
		$file = $this->getConfigPHPFilename();
		$this->assertNull($loader->loadFile($file, 'section-b'));

		$data = $loader->getFileData($file);
		$section = $loader->getSection($data, 'section-b');
		$this->assertEquals($section, ConfigRegistry::getAll());
		return $loader;
	}
}
