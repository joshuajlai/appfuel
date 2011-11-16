<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel\Startup;

use Appfuel\Kernel\IncludePath,
	Appfuel\Kernel\Error\ErrorLevel,
	Appfuel\Kernel\Error\ErrorDisplay,
	Appfuel\DataStructure\Dictionary,
	Appfuel\ClassLoader\StandardAutoLoader;

/**
 * Initializing the kernel involves the folling:
 *	setup the include path
 *	register autoloader
 *	set the timezone
 *	set erroring level
 *	enable or disable displaying errors
 *	register error handler
 *	register exception handler
 * 
 */
class KernelInitTask extends StartupTaskAbstract 
{
	/**
	 * Assign the registry keys to be pulled from the kernel registry
	 * 
	 * @return	KernelIntializeTask
	 */
	public function __construct()
	{
		$keys = array(
			'include-path-action',
			'include-path',
			'enable-autoloader',
			'display-errors',
			'error-reporting',
			'default-timezone',
		);
		$this->setRegistryKeys($keys);
	}

	/**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		$status  = '';
		$config  = new Dictionary($params);
        $display = $config->get('display-errors', 'on');
        if (null !== $display) {
            $errorDisplay = $this->createErrorDisplay();
            $errorDisplay->set($display);
			$status = 'display-errors,';
        }

		$defaultLevel = 'all,strict,';
        $level = $config->get('error-reporting', $defaultLevel);
        if (null !== $level) {
            $errorReporting = $this->createErrorLevel();
            $errorReporting->setLevel($level);
			$status .= 'error-reporting,';
        }

        $ipath   = $config->get('include-path', array(AF_BASE_PATH . "/lib"));
        $iaction = $config->get('include-path-action', 'replace');
        if (! empty($ipath)) {
            $includePath = $this->createIncludePath();
            $includePath->setPath($ipath, $iaction);
			$status .= 'include-path,';
        }

        $defaultTz = $config->get('default-timezone', 'America/Los_Angeles');
        if (null !== $defaultTz) {
            date_default_timezone_set($defaultTz);
			$status .= 'timezone,';
        }

        $enableAutoloader =(bool) $config->get('enable-autoloader', true);
        if (true === $enableAutoloader) {
            $autoloader = $this->createAutoloader();
            $autoloader->register();
			$status .= 'autoloader';
        }

		if (empty($status)) {
			$status = 'not initialized';
		}
		else {
			$status = "initialize: $status";
			$status = trim($status, ",");
		}

		$this->setStatus($status);
	}

    /**
     * @return  ErrorDisplay
     */
    protected function createErrorDisplay()
    {
        return new ErrorDisplay();
    }
    /**
     * @return  ErrorDisplay
     */
    protected function createErrorLevel()
    {
        return new ErrorLevel();
    }
    /**
     * @return  IncludePath
     */
    protected function createIncludePath()
    {
        return new IncludePath();
    }

    /**
     * @return  Autoloader
     */
    protected function createAutoloader()
    {
        return new StandardAutoloader();
    }
}	
