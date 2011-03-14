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

use Appfuel\Stdlib\Filesystem\Manager as FileManager;

/**
 * All Appfuel test cases will extend this class which provides features like
 * path locations, backup/restore autoloader, backup/restore include paths. 
 */
class AfTestCase extends \PHPUnit_Framework_TestCase
{
	/**
	 * Root path of the application
	 * @var string
	 */
	private $afBasePath = AF_BASE_PATH;

	/**
	 * Used to backup the current state of autoloaders
	 * @var array
	 */
	protected $bkAutoloadFunctions = array();

	/**
	 * Used to backup the include path
	 * @var string
	 */
	protected $bkIncludePath = NULL;

	/**
	 * @return null
	 */
	public function getBasePath()
	{
		return $this->afBasePath;
	}

	/**
	 * @return null
	 */
	public function getTestBase()
	{
		return $this->afBasePath . DIRECTORY_SEPARATOR . 'test';
	}

	/**
	 * @return AfTestCase
	 */
	public function backupAutoloaders()
	{
		$this->bkAutoloadFunctions = spl_autoload_functions();
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
		return $this->bkAutoloadFunctions;
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

	/**
	 * @return AfTestCase
	 */
	public function backupIncludePath()
	{
		$this->bkIncludePath = get_include_path();
		return $this;
	}

	/**
	 * @return bool
	 */
	public function restoreIncludePath()
	{
		$path = $this->getBackedUpIncludePath();
		if (! is_string($path) || empty($path)) {
			return  FALSE;
		}

		set_include_path($path);
		return TRUE;
	}

	/**
	 * @return string
	 */
	public function getBackedUpIncludePath()
	{
		return $this->bkIncludePath;
	}

	/**
	 * @return string
	 */
	public function getCurrentPath($relPath = NULL)
	{
		$class = get_class($this);
		$testPath = $this->getTestBase();
		$dir  = FileManager::classNameToDir($class);

		$full = $testPath . DIRECTORY_SEPARATOR . $dir;
		if (NULL !== $relPath && is_string($relPath) && ! empty($relPath)) {
			$full .= DIRECTORY_SEPARATOR . $relPath;
		}
		return $full;
	}
}

