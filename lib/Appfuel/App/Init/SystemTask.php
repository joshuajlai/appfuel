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
namespace Appfuel\App\Init;

use Appfuel\Framework\Registry,
	Appfuel\Framework\Exception,
    Appfuel\Framework\Env\Autoloader,
    Appfuel\Framework\Env\ErrorReporting,
    Appfuel\Framework\Env\ErrorDisplay,
    Appfuel\Framework\Env\IncludePath,
	Appfuel\Framework\App\Init\TaskInterface,
	Appfuel\Framework\DataStructure\DictionaryInterface;

/**
 * System task handles initialization of the php system
 */
class SystemTask implements TaskInterface
{
    /**  
     * @param   string		$file	file path to config ini
	 * @return	Env\State 
     */
	public function init()
	{
        $display = Registry::get('display_errors', null);
        if (null !== $display) {
            $errorDisplay = $this->createErrorDisplay();
            $errorDisplay->set($display);
        }

        $level = Registry::get('error_reporting', null);
        if (null !== $level) {
            $errorReporting = $this->createErrorReporting();
            $errorReporting->setLevel($level);
        }

        $ipath   = Registry::get('include_path', null);
        $iaction = Registry::get('include_path_action', null);
        if (null !== $ipath) {
            $includePath = $this->createIncludePath();
            $includePath->usePaths($ipath, $iaction);
        }

        $defaultTz = Registry::get('default_timezone', null);
        if (null !== $defaultTz) {
			date_default_timezone_set($defaultTz);
        }

        $enableAutoloader =(bool) Registry::get('enable_autoloader', null);
        if (true === $enableAutoloader) {
            $autoloader = $this->createAutoloader();
            $autoloader->register();
        }
		return true;
	}

	/**
	 * @return	ErrorDisplay
	 */
	protected function createErrorDisplay()
	{
		return new ErrorDisplay();
	}

	/**
	 * @return	ErrorDisplay
	 */
	protected function createErrorReporting()
	{
		return new ErrorReporting();
	}

	/**
	 * @return	IncludePath
	 */
	protected function createIncludePath()
	{
		return new IncludePath();
	}

	/**
	 * @return	Autoloader
	 */
	protected function createAutoloader()
	{
		return new Autoloader();
	}
}
