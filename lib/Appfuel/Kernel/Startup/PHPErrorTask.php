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
namespace Appfuel\Kernel\Startup;

use DomainException,
	Appfuel\Error\PHPErrorLevel;

/**
 * Used when you want a more readable interface for setting php error level
 */
class PHPErrorTask extends StartupTask 
{
	/**
	 * Set keys used to find the ini settings in the registry
	 *
	 * @return	PHPIniStartup
	 */
	public function __construct()
	{
		$this->setDataKeys(array(
			'php-display-errors'	=> 'off',
			'php-error-level'		=> 'all, strict'
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

		$status = '';
		if (isset($params['php-display-errors'])) {
			$display = $params['php-display-errors'];
			if (! in_array($display, array('on', 'off'), true)) {
				$err  = 'config setting for display errors can only be ';
				$err .= '-(on, off)';
				throw new DomainException($err);
			}
		
			ini_set('display_errors', $display);
			$status = "display_errors is set to -($display) ";
		}

		if (isset($params['php-error-level'])) {
			$code = $params['php-error-level'];
			if (! is_string($code) || empty($code)) {
				$err = 'error level must be a non empty string';
				throw new DomainException($err);
			}
			$errorLevel = new PHPErrorLevel();
			$errorLevel->setLevel($code);

			$status .= "level is set to -($code) ";
		}

		$this->setStatus($status);
	}
}
