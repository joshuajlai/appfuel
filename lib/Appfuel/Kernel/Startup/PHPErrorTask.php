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

use DomainException;

/**
 * Used when you want a more readable interface for setting php error level
 */
class PHPErrorTask extends StartupTaskAbstract 
{
	/**
	 * Set keys used to find the ini settings in the registry
	 *
	 * @return	PHPIniStartup
	 */
	public function __construct()
	{
		$this->setRegistryKeys(array(
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

		$display = 'off';
		if (isset($params['php-display-errors']) && 
			is_string($params['php-display-errors']) &&
			'on' === strtolower($params['php-display-errors'])) {
			$display = 'on';
		}
		ini_set('display_errors', $display);

		if (! isset($params['php-error-level'])) {
			$this->setStatus("display errors: $display error level: not set");
			return;
		}

	}
}
