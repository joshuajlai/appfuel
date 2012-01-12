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

use Appfuel\App\Resource\AssetList,
	TestFuel\TestCase\BaseTestCase;

/**
 * This is a file list that only allows asset files to be added
 */
class AssetTest extends BaseTestCase
{
	/**
	 * System Under Test
	 * @var JsList
	 */
	protected $list = null;

	/**
	 * @return	null
	 */
	public function setUp()
	{
		$this->list = new AssetList();
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
		$this->assertInstanceOf(
			'Appfuel\App\Resource\FileList',
			$this->list
		);
		
		$this->assertEquals('asset', $this->list->getType());
		$this->assertEquals(0, $this->list->count());
	}

	/**
	 * @expectedException	InvalidArgumentException
	 * @return				null
	 */
	public function testNonAssetFile()
	{
		$this->list->addFile('mycss.css');
	}

	/**
	 * @return	null
	 */
	public function testAddAssetFiles()
	{
		$files = array(
			'file1.png',
			'file2.jpg',
			'file3.gif',
			'file4.swf',
			'file5.ico',
		);
		$this->assertSame($this->list, $this->list->loadFiles($files));
		$this->assertEquals($files,	$this->list->getFiles());
	}

	/**
	 * Unlike js or css the AssetList is open to modifying the valid extension
	 * list
	 *
	 * @return	null
	 */
	public function testAddExtension()
	{
		/* we will add css only because we have a previous test where css
		 * fails
		 */
		$this->assertSame($this->list, $this->list->addAssetExtension('css'));
		$this->assertSame($this->list, $this->list->addFile('mycss.css'));

		$this->assertEquals(array('mycss.css'), $this->list->getFiles());
	}

    /**
     * @expectedException   InvalidArgumentException
     * @dataProvider        provideEmptyStrings
     * @depends             testInitialState
     * @return              null
     */
    public function testAddAssetExtensionEmptyString_Failure($ext)
    {  
        $this->list->addAssetExtension($ext);
    }

    /**
     * @expectedException   InvalidArgumentException
     * @dataProvider        provideInvalidStrings
     * @depends             testInitialState
     * @return              null
     */
    public function testAddAssetExtensionInvalidString_Failure($ext)
    {  
        $this->list->addAssetExtension($ext);
    }
}
