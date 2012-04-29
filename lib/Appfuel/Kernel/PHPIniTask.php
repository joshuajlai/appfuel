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

use DomainException;

/**
 * Calls ini_set on the key value pairs in the config registry
 */
class PHPIniTask extends StartupTask 
{
	/**
	 * Set keys used to find the ini settings in the registry
	 *
	 * @return	PHPIniStartup
	 */
	public function __construct()
	{
		$this->setDataKeys(array('php-ini' => null));
	}

	/**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		if (empty($params) || ! isset($params['php-ini'])) {
			return;
		}
		$data = $params['php-ini'];

		if (! is_array($data) || $data === array_values($data)) {
			$err = 'php ini settings must be an associative array of ';
			$err = 'ini varname => ini newvalue';
			throw new DomainException($err);
		}

		$count = 0;
		foreach ($data as $varname => $newvalue) {
			if (! is_string($varname) || empty($varname)) {
				$err = "ini name must non empty string: at index -($count)";
				throw new DomainException($err);
			}

			if (! is_scalar($newvalue)) {
				$err = "ini value must be a scalar value: at index -($count)";
				throw new DomainException($err);
			}

			ini_set($varname, $newvalue);
			$count++;
		}

		$this->setStatus("initialized $count php ini settings");
	}
}
