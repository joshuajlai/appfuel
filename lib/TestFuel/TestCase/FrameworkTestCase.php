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

/**
 * Specialized test case which handles conditions for testing the framework
 * initialization
 */
class FrameworkTestCase extends BaseTestCase 
{
	/**
	 * Always ensures we have at the very least full errors that are turned
	 * on
	 *
	 * @return null
	 */
	public function setUp()
	{
		error_reporting(E_ALL | E_STRICT);
		ini_set('error_diplay', 'on');
	}

	/**
	 * @return	null
	 */
	public function tearDown()
	{
		$this->restoreKernelState();
	}

	/**
	 * Did not have to trouble shoot why phpunit expectes me to have a test
	 * here!
	 * 
	 * @return null
	 */
	public function testTrue()
	{
		$this->assertTrue(true);
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
}
