<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel;

use DomainException,
	Appfuel\Kernel\FaultHandlerInterface;

/**
 * register php error_handler and exception_handler
 */
class FaultHandlerTask extends StartupTask
{
	/**
	 * @return	PHPIniStartup
	 */
	public function __construct()
	{
		$this->setRegistryKeys(array(
			'php-error-handler'		=> null,
			'php-exception-handler' => null,
			'fault-handler-class'	=> null
		));
	}

	/**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		if (empty($params)) {
			return;
		}
		
		if (isset($params['fault-handler-class'])) {
			$class = $params['fault-handler-class'];
			if (! is_string($class) || empty($class)) {
				$err = "fault-handler-class must be a non empty string";
				throw new DomainException($err);
			}
			
			$handler = new $class();
			if (! $handler instanceof FaultHandlerInterface) {
				$err  = 'fault handler must implment -(Appfuel\Kernel';
				$err .= '\FaultHandlerInterface';
				throw new DomainException($err);
			}
			
            set_error_handler(array($handler, 'handleError'));
            set_exception_handler(array($handler, 'handleException'));
			$this->setStatus("fault handler registered is -($class)");
			return;
		}

		if (isset($params['php-error-handler'])) {
			$data = $params['php-error-handler'];
			if (! is_array($data)) {
				$err  = "error handler data must be an array of at most ";
				$err .= "two items: 1) callable handler 2) bitwise mask ";
				$err .= "used to limit which errors are triggered";
				throw new DomainException($err);
			}

			$func = current($data);
			$mask = next($data);
			if (is_int($mask) && $mask > 0) {
				set_error_handler($func, $mask);
			}
			else {
				set_error_handler($func);
			}

			$this->setStatus("error handler was manual registered");
		}

		if (isset($params['php-exception-handler'])) {
			$func = $params['php-exception-handler'];
			if (! is_callable($func)) {
				$err  = "exception handler data must be callable";
				throw new DomainException($err);
			}

			set_error_handler($func);

			$this->setStatus("exception handler was manual registered");
		}
	}
}
