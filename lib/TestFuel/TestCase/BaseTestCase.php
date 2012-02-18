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
namespace TestFuel\TestCase;

use StdClass,
	TestFuel\Provider\StringProvider,
	Appfuel\Kernel\PathFinder,
	Appfuel\Kernel\KernelRegistry,
	PHPUnit_Extensions_OutputTestCase;

/**
 * All Appfuel test cases will extend this class which provides features like
 * path locations, backup/restore autoloader, backup/restore include paths. 
 */
class BaseTestCase extends PHPUnit_Extensions_OutputTestCase
{
	/**
	 * @var StringProvider
	 */
	protected $stringProvider = null;

	/**
	 * @var PathFinder
	 */
	protected $pathFinder = null;

    /**
     * @return  BaseTestCase
     */
    public function __construct($name = null,
                                array $data = array(),
                                $dataName = '')
    {
		$this->pathFinder = new PathFinder('test');  
		$this->stringProvider = new StringProvider();
        parent::__construct($name, $data, $dataName);
    }

	/**
	 * @return	StringProvider
	 */
	public function getStringProvider()
	{
		return $this->stringProvider;
	}

	/**
	 * @return	PathFinder
	 */
	public function getPathFinder()
	{
		return $this->pathFinder;
	}

	public function getTestFilesPath()
	{
		return $this->getPathFinder()
					->getPath('files');
	}

	/**
	 * Always have full error reporting and errors turned on
	 * 
	 * @return	null
	 */
	public function setUp()
	{
        error_reporting(E_ALL | E_STRICT);
        ini_set('error_diplay', 'on');
		$this->clearKernelRegistry();
	}

	/**
	 * @return null
	 */
	public function tearDown()
	{
		$this->restoreKernelState();
		$this->restoreKernelRegistry();
	}

	/**
	 * @return null
	 */
	public function clearKernelRegistry()
	{
		KernelRegistry::clear();
	}

	/**
	 * Restore all the kernel registry settings with the settings backup 
	 * that occured in the UnitTestStartup strategy
	 *
	 * @return	null
	 */
	public function restoreKernelRegistry()
	{
		KernelRegistry::setParams(TestRegistry::getKernelParams());
		KernelRegistry::setDomainMap(TestRegistry::getKernelDomainMap());
	}

    /**
     * Restore the kernel state to it's original values
     *
     * @return null
     */
    public function restoreKernelState()
    {
        $state = TestRegistry::getKernelState();
        error_reporting($state->getErrorReporting());
        date_default_timezone_set($state->getDefaultTimezone());
        ini_set('error_display', $state->getDisplayError());
        set_include_path($state->getIncludePath());

        $functions = $state->getAutoloadStack();
		$this->clearAutoloaders();
        foreach ($functions as $item) {
            if (is_string($item)) {
                spl_autoload_register($item);
            } else if (is_array($item) && 2 === count($item)) {
                spl_autoload_register(array($item[0], $item[1]));
            }
        }
    }

    /**
     * Restore autoloader to its previous state
     * 
     * @return null
     */
    public function restoreAutoloaders()
    {
        $state = TestRegistry::getKernelState();
        $functions = $state->getAutoloadStack();
		$this->clearAutoloaders();
        foreach ($functions as $item) {
            if (is_string($item)) {
                spl_autoload_register($item);
            } else if (is_array($item) && 2 === count($item)) {
                spl_autoload_register(array($item[0], $item[1]));
            }
        }
    }

    /**
     * Remove registered autoloader functions. Note that this does not
     * backup those functions
     *
     * @return AfTestCase
     */
    public function clearAutoloaders()
    {
        $functions = spl_autoload_functions();
        foreach ($functions as $item) {
            if (is_string($item)) {
                spl_autoload_unregister($item);
            } else if (is_array($item) && 2 === count($item)) {
                spl_autoload_unregister(array($item[0], $item[1]));
            }
        }
    }

	/**
	 * Restore the include path to the original kernel state
	 *
	 * @return	null
	 */
	public function restoreIncludePath()
	{
        $state = TestRegistry::getKernelState();
        set_include_path($state->getIncludePath());
	}

	/**
	 * @return	array
	 */
	public function provideEmptyStrings()
	{
		$provider = $this->getStringProvider();
		return $provider->provideEmptyStrings();
	}

	/**
	 * @return	array
	 */
	public function provideNonEmptyStrings()
	{
		$provider = $this->getStringProvider();
		return $provider->provideNonEmptyStrings();
	}

	/**
	 * @return	array
	 */
	public function provideNonEmptyStringsNoNumbers()
	{
		$provider = $this->getStringProvider();
		return $provider->provideNonEmptyStrings(false);
	}

	/**
	 * @return	array
	 */	
	public function provideAllStringsIncludingCastable()
	{
		$provider = $this->getStringProvider();
		return $provider->provideAllStrings();
		
	}

	/**
	 * @return	array
	 */	
	public function provideEmptyNonEmptyAndToString()
	{
		$provider = $this->getStringProvider();
		return $provider->provideEmptyNonEmptyAndToString();
		
	}

	/**
	 * @return	array
	 */
	public function provideNoCastableStrings()
	{
		return $this->getStringProvider()
					->provideNoCastableStrings();
	}

	/**
	 * @return	array
	 */
	public function provideInvalidStringsIncludeNull()
	{
		$provider = $this->getStringProvider();
		$includeNull = true;
		return $provider->provideStrictInvalidStrings($includeNull);
	}

    /**
     * @return  array
     */
    public function provideInvalidStrings()
    {
        return array(
            array(12345),
            array(1.234),
            array(true),
            array(false),
            array(array(1,2,3)),
            array(new StdClass())
        );
    }

    /**
     * @return  array
     */
    public function provideInvalidArray()
    {  
        return array(
            array(12345),
            array(1.234),
            array(true),
            array(false),
            array(''),
            array('this is a string'),
            array(new StdClass())
        );
    }
}
