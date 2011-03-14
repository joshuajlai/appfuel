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
namespace Test;

/**
 * 
 */
class AfTestCase extends \PHPUnit_Framework_TestCase
{
	/**
	 * Absolute path to the testing directory
	 */
	protected $testPath = NULL;

	/**
	 * Root path of the application
	 * @var string
	 */
	protected $basePath = AF_BASE_PATH;

	/**
	 * Used to backup the current state of autoloaders
	 * @var array
	 */
	protected $autoloadFunctions = array();

	/**
	 * @return null
	 */
	public function getBasePath()
	{
		return $this->basePath;
	}

	/**
	 * @return null
	 */
	public function getTestBase()
	{
		return $this->basePath . DIRECTORY_SEPARATOR . 'test';
	}

	/**
	 * @return AfTestCase
	 */
	public function backupAutoloaders()
	{
		$this->autoloadFunctions = spl_autoload_functions();
		return $this;
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
	 * @return array
	 */
	public function getBackedUpAutoloaders()
	{
		return $this->autoloadFunctions;
	}

	/**
	 * @return AfTestCase
	 */
	public function restoreAutoloaders()
	{
		$functions = $this->getBackedUpAutoloaders();
		foreach ($functions as $item) {
			if (is_string($item)) {
				spl_autoload_register($item);
			} else if (is_array($item) && 2 === count($item)) {
				spl_autoload_register(array($item[0], $item[1]));
			}
		}

		return $this;
	}

}

