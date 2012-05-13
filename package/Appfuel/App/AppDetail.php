<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\App;

use DomainException;

/**
 * Handle path information for the following
 * <base-path>  : absolute path to the applications root directory
 * www			: the web root directory
 * bin			: cli scripts
 * test			: unit test bootrapping and supporting files
 * package		: php source code
 * resource		: clientside resource files js,css,html,phtml etc...
 * routes		: route specification files 
 * config		: config files 
 * datasource	: mappings for database, webservices, files etc..
 * build		: system generated files	
 * 
 * Allows the application dir structure change without changing the
 * kernel code.
 */
class AppDetail implements AppDetailInterface
{
	/**
	 * @var string
	 */
	protected $base = null;
	
	/**
	 * @var string
	 */
	protected $www = 'www';

	/**
	 * @var string
	 */
	protected $bin = 'bin';

	/**
	 * @var string
	 */
	protected $test = 'test';

	/**
	 * @var string
	 */
	protected $package = 'package';

	/**
	 * @var string
	 */
	protected $resource = 'resource';

	/**
	 * @var string
	 */
	protected $route = 'route';

	/**
	 * @var string
	 */
	protected $config = 'config';

	protected $configFiles = array(
		'web'  => 'web-config.json',
		'cli'  => 'cli-config.json',
		'test' => 'test-config.json'
	);

	/**
	 * @var string
	 */
	protected $datasource = 'datasource';
	
	/**
	 * @var string
	 */
	protected $build = 'app/build';

	/**
	 * @param	string	$basePath
	 * @return	AppDirStructure
	 */
	public function __construct($base)
	{
		if (! is_string($base) || ! ($base = trim($base))) {
			$err = "base path must be a non empty string";
			throw new DomainException($err);
		}

		$this->base = $base;
	}

	/**
	 * @return	string
	 */
	public function getBasePath($path = null)
	{
		$base = $this->base;
		return (null === $path) ? 
			$base : $base . DIRECTORY_SEPARATOR . $path;
	}

	/**
	 * @param	bool	$isBase
	 * @return	string
	 */
	public function getBin($isBase = true)
	{
		return $this->resolvePath($this->bin, $isBase);
	}

	/**
	 * @param	bool	$isBase
	 * @return	string
	 */
	public function getWWW($isBase = true)
	{
		return $this->resolvePath($this->www, $isBase);
	}

	/**
	 * @param	bool	$isBase
	 * @return	string
	 */
	public function getTest($isBase = true)
	{
		return $this->resolvePath($this->test, $isBase);
	}

	/**
	 * @param	bool	$isBase
	 * @return	string
	 */
	public function getPackage($isBase = true)
	{
		return $this->resolvePath($this->package, $isBase);
	}

	/**
	 * @param	bool	$isBase
	 * @return	string
	 */
	public function getResource($isBase = true)
	{
		return $this->resolvePath($this->resource, $isBase);
	}

	/**
	 * @param	bool	$isBase
	 * @return	string
	 */
	public function getConfig($isBase = true)
	{
		return $this->resolvePath($this->config, $isBase);
	}

	/**
	 * @param	string	$entry
	 * @return	string
	 */
	public function getConfigFile($entry)
	{
		if (! is_string($entry) || ! isset($this->configFiles[$entry])) {
			$list = implode(',', array_keys($this->configFiles));
			$err  = "entry point must be one of -($list)";
			throw new DomainException($err);
		}

		return $this->getBuild(false) . DIRECTORY_SEPARATOR .
			   $this->configFiles[$entry];
	}

	/**
	 * @param	bool	$isBase
	 * @return	string
	 */
	public function getDataSource($isBase = true)
	{
		return $this->resolvePath($this->datasource, $isBase);
	}

	/**
	 * @param	bool	$isBase
	 * @return	string
	 */
	public function getBuild($isBase = true)
	{
		return $this->resolvePath($this->build, $isBase);
	}

	/**
	 * @param	string	$path
	 * @param	bool	$isBase
	 * @return	string
	 */
	protected function resolvePath($path, $isBase = true)
	{
		return (true === $isBase) ? $this->getBasePath($path): $path;
	}
}
