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
	RunTimeException,
	TestFuel\Provider\StringProvider,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Kernel\KernelStateInterface,
	Appfuel\DataSource\Db\DbStartupTask,
	Appfuel\Validate\ValidationFactory,
	PHPUnit_Framework_TestCase;

/**
 * All Appfuel test cases will extend this class which provides features like
 * path locations, backup/restore autoloader, backup/restore include paths. 
 */
class BaseTestCase extends PHPUnit_Framework_TestCase
{
	/**
	 * @var PathFinder
	 */
	protected $fileFinder = null;

	/**
	 * back of the static validation map the validation factory uses to create
	 * its objects
	 * @var array	
	 */
	protected $bkValidation = array(
		'validator' => array(),
		'filter'    => array()
	);

    /**
     * @return  BaseTestCase
     */
    public function __construct($name = null,
                                array $data = array(),
                                $dataName = '')
    {
		$this->pathFinder = new FileFinder('test');  
        parent::__construct($name, $data, $dataName);
    }

    /**
     * @return string
     */
    public function getErrorInterface()
    {
        return 'Appfuel\Error\ErrorInterface';
    }

	/**
	 * @return	PathFinder
	 */
	public function getFileFinder()
	{
		return $this->fileFinder;
	}

	public function getTestFilesPath()
	{
		return $this->getFileFinder()
					->getPath('files');
	}

	/**
	 * @return	string
	 */
	public function getBasePath()
	{
		return AF_BASE_PATH;
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
	}

	/**
	 * @return	BaseTestCase
	 */
	public function backupValidationMap()
	{
		$this->bkValidation['validator'] = ValidationFactory::getValidatorMap();
		$this->bkValidation['filter'] = ValidationFactory::getFilterMap();
	}

	/**
	 * @return	BaseTestCase
	 */
	public function restoreValidationMap()
	{
		$vmap = $this->bkValidation['validator'];
		ValidationFactory::setValidatorMap($vmap);

		$fmap = $this->bkValidation['validator'];
		ValidationFactory::setFilterMap($vmap);

		return $this;
	}

    /**
     * Restore the kernel state to it's original values
     *
     * @return null
     */
    public function restoreKernelState()
    {
        $state = TestRegistry::getKernelState();
		if (! $state instanceof KernelStateInterface) {
			$err  = 'kernel state has not been set in the test registry ';
			$err .= 'it is likely that the UnitTestStartup has not been run';
			throw new RunTimeException($err);
		}
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
	 * @return null
	 */
	public function runDbStartupTask()
	{
		$task = new DbStartupTask();
		$keys = $task->getRegistryKeys();
		$params = array();
		foreach ($keys as $key => $default) {
			$params[$key] = KernelRegistry::getParam($key, $default);
		}

		$task->execute($params);
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
	 * @param	array	$more
	 * @param	bool	$isEmpty
	 * @return	array
	 */
	public function provideInvalidStrings()
	{
		return StringProvider::getInvalidStrings();
	}

	public function provideInvalidStringsIncludeEmpty()
	{
		return StringProvider::getInvalidStrings(true);
	}
}
