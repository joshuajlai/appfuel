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
     * @return  BaseTestCase
     */
    public function __construct($name = null,
                                array $data = array(),
                                $dataName = '')
    {  
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
		KernelRegistry::setRouteMap(TestRegistry::getKernelRouteMap());
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
	public function provideAllStringsIncludingCastable()
	{
		$provider = $this->getStringProvider();
		return $provider->provideAllStrings();
		
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
		return $provider->provideInvalidStrings($includeNull);
	}

	/**
	 * @return	array
	 */
	public function provideInvalidStrings()
	{
		$provider = $this->getStringProvider();
		$includeNull = false;
		return $provider->provideInvalidStrings($includeNull);
	}

	/**
	 * @return	array
	 */
	public function provideInvalidArray()
	{
		return array(
			array(new StdClass()),
			array(12345),
			array(1.234),
			array(false),
			array(true)
		);
	}
}
