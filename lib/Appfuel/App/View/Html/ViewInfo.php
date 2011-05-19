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
namespace Appfuel\App\View\Html;

/**
 * Value object used to hold the information needed to build clientside 
 * resources into script and link tags for the html page.
 */
class ViewInfo
{
	/**
	 * Path used to locate resources directory and build files
	 * @var string
	 */
	protected $path = null;
	
	/**
	 * Name of the javascript/css file. This is used in the build system, all
	 * page level resources will be rolled into one css/js file
	 * @var string
	 */
	protected $jsName = null;
	
	/**
	 * Name of the global js and/or css resource
	 * @var string
	 */
	protected $globalName = 'global';

	/**
	 * @param	string	$path
	 * @param	string	$jsName
	 * @param	string  $cssName
	 * @param	string	$globalName
	 * @return	ViewInfo
	 */
	public function __construct($path, $resourceName, $globalName = null)
	{
		$err = 'Invalid Parameter:';
		if (! is_string($path) || empty($path)) {
			throw new Exception("$err path must be a non empty string");
		}
		$this->path = $path;

		if (! is_string($resourceName) || empty($resourceName)) {
			throw new Exception("$err Js name  must be a non empty string");
		}
		$this->resourceName = $resourceName;

		if (is_string($globalName) && ! empty($globalName) {
			$this->globalName = $globalName;
		}
	}

	/**
	 * @return	string	
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @return string
	 */
	public function getResourceName()
	{
		return $this->resourceName;
	}

	/**
	 * @return string
	 */
	public function getGlobalName()
	{
		return $this->resourceName;
	}
}
