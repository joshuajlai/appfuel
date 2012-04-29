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
 * Set the default timezone
 */
class PHPDefaultTimezoneTask extends StartupTask
{
	/**
	 * @return	PHPDefaultTimeTask
	 */
	public function __construct()
	{
		$this->setDataKeys(array('php-default-timezone'	=> null));
	}

	/**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		$msg = '';
		if (isset($params['php-default-timezone'])) {
			$zone = $params['php-default-timezone'];
			if (! is_string($zone) || empty($zone)) {
				$err = 'timezone must be a non empty string';
				throw new DomainException($err);
			}
			date_default_timezone_set($zone);
			$msg = "default timezone was set to -($zone)";
		}

		$this->setStatus($msg);
	}
}
