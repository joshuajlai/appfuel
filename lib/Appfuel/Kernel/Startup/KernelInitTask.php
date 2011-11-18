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
		$this->setStatus('my message');
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
