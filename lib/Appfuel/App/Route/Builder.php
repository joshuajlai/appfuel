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
namespace Appfuel\App\Route;

use Appfuel\Framework\Exception,
	Appfuel\App\Registry,
	Appfuel\Stdlib\Filesystem\Manager as FileManager;

/**
 * Value object used to hold routing information
 */
class Builder
{
	/**
	 * Path to the ini file that holds all the routes
	 * @var string 
	 */
	protected $iniPath = null;

	/**
	 * @param	string	$path
	 * @return	Finder
	 */
	public function __construct($path)
	{
		$this->iniPath = $path;
	}

	/**
	 * @return string
	 */
	public function getIniPath()
	{
		return $this->iniPath;
	}

	/**
	 * Locate the route stored in the ini file
	 *
	 * @param	string	$routeString	
	 * @return	Route
	 */
	public function build($routeString)
	{
		$routes = FileManager::parseIni($this->getIniPath());
		if (! array_key_exists($routeString, $routes)) {
			return false;
		}

		$data = $routes[$routeString];
		$err = "Route build failed: ";
		if (! is_string($data) || empty($data)) {
			throw new Exception("$err ini data invalid or malformed");
		}
		
		$data = explode(',', $data);
		if (empty($data) ||  count($data) < 3) {
			throw new Exception("$err route exists but is malformed");
		}

		$namespace = trim($data[0]);
		if (! is_string($namespace) || empty($namespace)) {
			throw new Exception("$err namespace must be a non empty string");
		}

		$access = trim($data[1]);
		if (! is_string($access) || empty($access)) {
			throw new Exception("$err access must be a non empty string");
		}

		$return = trim($data[2]);
		if (! is_string($return) || empty($return)) {
			throw new Exception("$err return type  must be a non empty string");
		}

		return new ActionRoute($routeString, $namespace, $access, $return);
	}
}
