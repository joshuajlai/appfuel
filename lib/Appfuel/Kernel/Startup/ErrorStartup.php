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

use Appfuel\Kernel\Error\ErrorLevel,
	Appfuel\Kernel\Error\ErrorDisplay;

/**
 * Initializing the kernel involves the folling:
 */
class ErrorStartup extends StartupTaskAbstract 
{
	/**
	 * Assign the registry keys to be pulled from the kernel registry
	 * 
	 * @return	KernelIntializeTask
	 */
	public function __construct()
	{
		$keys = array(
			'display-errors',
			'error-reporting',
		);
		$this->setRegistryKeys($keys);
	}

	/**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		if (isset($params['display-error']) && 
			is_string($params['display-error'])) {
            $errorDisplay = new Error\ErrorDisplay();
            $errorDisplay->set($params['display-error']);
		}
        $level = 'all,strict';
		if (isset($params['error-level'])) {
			$level = $params['error-level'];
        }

        $errorReporting = new Error\ErrorLevel();
        $errorReporting->setLevel($level);

		$this->setStatus('error display and reporting: initialized');
	}
}	
