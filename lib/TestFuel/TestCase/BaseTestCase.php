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
	Appfuel\Kernel\KernelRegistry,
	PHPUnit_Extensions_OutputTestCase;

/**
 * All Appfuel test cases will extend this class which provides features like
 * path locations, backup/restore autoloader, backup/restore include paths. 
 */
class BaseTestCase extends PHPUnit_Extensions_OutputTestCase
{
    /**
     * @return  BaseTestCase
     */
    public function __construct($name = null,
                                array $data = array(),
                                $dataName = '')
    {  
        parent::__construct($name, $data, $dataName);
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
        $state = $this->getEnvState();
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
	 * @return	array
	 */
	public function provideInvalidStringsIncludeNull()
	{
		$data = $this->provideInvalidStrings();
		array_push($data, array(null));
		return $data;
	}

	/**
	 * @return	array
	 */
	public function provideInvalidStrings()
	{
		return array(
			array(0), 
			array(-1), 
			array(1), 
			array(12345), 
			array(1.23454), 
			array(true), 
			array(false), 
			array(new StdClass()),
			array(array()), 
			array(array(1)),
			array(array(1,2,3)),
		);
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
