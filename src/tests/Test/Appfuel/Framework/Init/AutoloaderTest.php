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
namespace Test\Appfuel\Framework\Init;

use Appfuel\Framework\Init\Includepath;

/**
 * 
 */
class IncludepathTest extends \PHPUnit_Framework_TestCase
{
    /**
     * System Under Test
     * @var \Appfuel\Framework\Init\Includepath
     */
    protected $includePath = NULL;

    /**
     * Backup for include path
     * @var string
     */
    protected $bkPaths = NULL;

    /**
     * Clear out the include path so we can test it being set
     * @return void
     */
    public function setUp()
    {
        $this->bkPaths = get_include_path();
        $this->includePath = new Includepath();
    }

    public function tearDown()
    {
        unset($this->includePath);
        set_include_path($this->bkPaths);
    }

	/**
	 * @return void
	 */
	public function restoreIncludePath()
	{
        set_include_path($this->bkPaths);
	}

    /**
     * Test init with default action. when only paths are past in the default
	 * action is to replace the include path with the paths
	 *
     * @return NULL
     */
    public function testInitializeReplace()
    {
		$params = array(
			'paths' => array(
				'/path_1',
				'/path_2'
			),
		);

		$expected = implode(PATH_SEPARATOR, $params['paths']); 
        $result = $this->includePath->init($params);
	
		$newPathString = get_include_path();

		/* restore the real include path to ensure phpunit runs */
		$this->restoreIncludePath();
		
		/* assert that the return value is the old path */
        $this->assertEquals($this->bkPaths, $result);

		/* assert the side effect of the init is the expected value */
        $this->assertEquals($expected, $newPathString);
		
    }

	/**
	 * We are now going to initialize with the paths being appended
	 * to the original include path
	 *
	 * @return NULL
	 */
    public function testInitializeAppend()
    {
		$params = array(
			'paths' => array(
				'/path_1',
				'/path_2'
			),
			'action' => 'append'
		);

		
		$expected = $this->bkPaths . PATH_SEPARATOR . 
					implode(PATH_SEPARATOR, $params['paths']); 
        
		$result = $this->includePath->init($params);
	
		$newPathString = get_include_path();

		/* restore the real include path to ensure phpunit runs */
		$this->restoreIncludePath();
		
		/* assert that the return value is the old path */
        $this->assertEquals($this->bkPaths, $result);

		/* assert the side effect of the init is the expected value */
        $this->assertEquals($expected, $newPathString);
    }

	/**
	 * We are now going to initialize with the paths being prepended
	 * to the original include path
	 *
	 * @return NULL
	 */
    public function testInitializePrepend()
    {
		$params = array(
			'paths' => array(
				'/path_1',
				'/path_2'
			),
			'action' => 'prepend'
		);

		
		$expected = implode(PATH_SEPARATOR, $params['paths']) .
					PATH_SEPARATOR . $this->bkPaths;
        
		$result = $this->includePath->init($params);
	
		$newPathString = get_include_path();

		/* restore the real include path to ensure phpunit runs */
		$this->restoreIncludePath();
		
		/* assert that the return value is the old path */
        $this->assertEquals($this->bkPaths, $result);

		/* assert the side effect of the init is the expected value */
        $this->assertEquals($expected, $newPathString);
    }

	/**
	 * False is returned when the paths are not identified by the 
	 * label 'paths'
	 *
	 * @return NULL
	 */
	public function testInializeNoPathsLabel()
	{
		$params = array(
			'otherLabel' => array(
				'/path_1',
				'/path_2'
			),
			'action' => 'prepend'
		);

		$result = $this->includePath->init($params);
		$this->assertFalse($result);	
	}

	/**
	 * False is returned when parameters are not given
	 *
	 * @return NULL
	 */
	public function testInializeNoParams()
	{
		$result = $this->includePath->init();
		$this->assertFalse($result);	
	}




}

