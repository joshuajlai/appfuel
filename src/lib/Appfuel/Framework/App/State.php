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
namespace Appfuel\Framework\App;

use Appfuel\Framework\Exception,
	Appfuel\Stdlib\Data\BagInterface,
	Appfuel\Stdlib\Data\Bag;

/**
 * Only responsibility to report on the state of the server settings
 */
class Setting
{
	/**
	 * Used to hold the data which represents state properties 
	 * @return string
	 */
	protected $bag  = null;

	/**
	 * Key to be used to identify the various state settings
	 * @var array
	 */
	protected $keys = array(
		'include_path',
        'include_path_action',
        'display_errors',
        'error_reporting',
        'enable_autoloader',
        'default_timezone'
	);
	
	/**
	 * use a bag to save the settings 
	 * @param	mixed	$data
	 * @return	State
	 */
	public function __construct()
	{
		$this->bag = new Bag($data);
	}

	
	/**
	 * Generally used when you need to automate pulling these settings out
	 * of a array
	 * 
	 * @return	array
	 */
	public function getkeys()
	{
		return $this->keys;
	}

	/**
	 *
	 * @return string
	 */
	public function displayErrors($flag)
	{
		if (null === $flag) {
			return ini_get('display_errors');
		}

		$this->getBag()
			 ->add('display_errors', $flag);

		return ini_set('display_errors', $flag);
	}

	/**
	 * @return string
	 */
	public function errorReporting($level)
	{
		return $this->get('error_reporting', null);
	}

	/**
	 * @return string
	 */
	public function defaultTimezone($timezone)
	{
		return $this->get('default_timezone', null);
	}

	/**
	 * @return string
	 */
	public function includePath($path, $action = 'replace')
	{
		return $this->get('include_path', null);
	}

	public function enableAutoloader()
	{

	}

	/**
	 * @return	BagInterface
	 */
	protected function getBag()
	{
		return $this->bag;
	}
}
