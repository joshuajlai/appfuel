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
 * set the php include path
 */
class PHPPathTask extends StartupTask
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
		$this->setDataKeys(array(
			'php-include-path'		  => null,
			'php-include-path-action' => $this->getDefaultAction(),
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
		if (empty($params) || ! isset($params['php-include-path'])) {
			return;
		}
		$paths = $params['php-include-path'];

        /* a single path was passed in */
        if (is_string($paths) && ! empty($paths)) {
            $path = $paths;
        } else if (is_array($paths) && ! empty($paths)) {
            $path= implode(PATH_SEPARATOR, $paths);
        } else {
			$err = 'include path can only be a string or an array of strings';
			throw new DomainException($err);
        }

		$action = $this->getDefaultAction();
		if (isset($params['php-include-path-action'])) {
			$action = $params['php-include-path-action'];
		}

		if (! $this->isValidAction($action)) {
			$err = "action must be -(append, prepend, replace)";
			throw new DomainException($err);
		}

        $includePath = get_include_path();
        if ('append' === $action) {
            $path = $includePath . PATH_SEPARATOR . $path;
        } else if ('prepend' === $action) {
            $path .= PATH_SEPARATOR . $includePath;
        }

        set_include_path($path);

		$this->setStatus("include path set with -($path)");
	}
}
