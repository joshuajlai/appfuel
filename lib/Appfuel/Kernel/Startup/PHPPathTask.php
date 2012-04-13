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
 * Calls ini_set on the key value pairs in the config registry
 */
class PHPIniTask extends StartupTaskAbstract 
{
	/**
	 * When no action is given paths will be appended to the existing paths
	 * @var string
	 */
	protected $defaultAction = 'append';

	/**
	 * Set keys used to find the ini settings in the registry
	 *
	 * @return	PHPIniStartup
	 */
	public function __construct()
	{
		$this->setRegistryKeys(array(
			'include-path'		  => null,
			'include-path-action' => $this->getDefaultAction(),
		));
	}

	/**
	 * @param	string	$action
	 * @return	null
	 */
	public function setDefaultAction($action)
	{
		if (! $this->isValidAction($action)) {
			$err  = "action must be one of the following: ";
			$err .= "-(append, prepend, replace) case matters";
			throw new DomainException($err);
		}

		$this->defaultAction = $action;
	}

	/**
	 * @return	string
	 */
	public function getDefaultAction()
	{
		return $this->defaultAction;
	}

	/**
	 * @param	string	$action
	 * @return	bool
	 */
	public function isValidAction($action)
	{		
		$valid  = array('append', 'prepend', 'replace');
		if (! is_string($action) || ! in_array($action, $valid, true)) {
			return false;
		}

		return true;
	}

	/**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
		if (empty($params) || ! isset($params['include-path'])) {
			return;
		}
		$paths = $params['include-path'];

        /* a single path was passed in */
        if (is_string($paths) && ! empty($paths)) {
            $pathString = $paths;
        } else if (is_array($paths) && ! empty($paths)) {
            $pathString = implode(PATH_SEPARATOR, $paths);
        } else {
            $this->setStatus("no path was set: arg was not a string or array");
        }

		$action = $this->getDefaultAction();
		if (isset($params['include-path-action'])) {
			$action = $params['include-path-action'];
		}

		if (! $this->validAction($action)) {
			$err = "action must be -(append, prepend, replace)";
			throw new DomainException($err);
		}

        $includePath = get_include_path();
        if ('append' === $action) {
            $pathString = $includePath . PATH_SEPARATOR . $pathString;
        } else if ('prepend' === $action) {
            $pathString .= PATH_SEPARATOR . $includePath;
        }

        set_include_path($pathString);

		$this->setStatus("include path set with -($pathString)");
	}
}
