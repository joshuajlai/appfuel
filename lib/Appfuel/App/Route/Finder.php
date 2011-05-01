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
	Appfuel\Stdlib\Filesystem\Manager as FileManager,

/**
 * Value object used to hold routing information
 */
class Builder
{
	/**
	 * Path to the ini file that holds all the routes
	 * @var string 
	 */
	protected $path = null;

	/**
	 * @param	string	$path
	 * @return	Finder
	 */
	public function __construct($path)
	{
		$this->path = $path;
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Locate the route stored in the ini file
	 *
	 * @param	string	$routeString	
	 * @return	Route
	 */
	public function find($routeString)
	{
		$routeString = str_replace('/', '-', $routeString);
		$routes = FileManager::parseIni($this->getPath());
		if (! array_key_exists($routeString, $routes)) {
			return false;
		}

		$data = $routes[$routeString];
		$route = $this->build($data);	
	}

	public function build($data)
	{

	}
}
