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
namespace Appfuel\App\Resource;

use InvalidArgumentException,
	Appfuel\Kernel\PathFinderInterface;

/**
 */
class Yui3DependencyResolver
{
	/**
	 * @var	string
	 */
	protected $mode = 'min';

	/**
	 * Datastructure used to hold dependency relationships
	 * @var array
	 */
	protected $graph = array();

	/**
	 * Path finder used to resolve patht to the build dir. Needed for
	 * processing css and assets
	 * @var PathFinder
	 */
	protected $finder = null;

	/**
	 * Holds all the resolved javascript paths
	 * @var	array
	 */
	protected $js = array();

	/**
	 * Holds all the resolved css paths
	 * @var array
	 */
	protected $css = array();

	/**
	 * Holds all the resolved asset paths
	 * @var array
	 */
	protected $assets = array();

	/**
	 * @param	array	$graph		
	 * @param	string	$mode	determines which file to use min, raw, debug
	 * @return	DependencyResolver
	 */
	public function __construct($depFile, PathFinderInterface $finder)
	{
		if (! is_string($depFile) || !($depFile = trim($depFile))) {
			$err = 'dependency file path must be non empty string';
			throw new InvalidArgumentException($err);
		}
		
		$fullPath = $finder->getPath($depFile);
		if (! file_exists($fullPath)) {
			$err = "dependency file not found at -($fullPath)";
			throw new RunTimeException($err);
		}
		$this->graph = json_decode(file_get_contents($fullPath), true);
		$this->finder = $finder;
	}

	/**
	 * @return	array
	 */
	public function getGraph()
	{
		return $this->graph;
	}

	/**
	 * @return	array
	 */
	public function getModules()
	{
		return array_keys($this->graph);
	}

	/**
	 * @param	string	$name
	 * @return	array | false when not found
	 */
	public function getModule($name)
	{
		if (! $this->isModule($name)) {
			return false;
		}

		return $this->graph[$name];
	}

	/**
	 * @return	int
	 */
	public function getModuleCount()
	{
		return count($this->graph);
	}

	/**
	 * @param	string	$name
	 * @return	bool
	 */
	public function isModule($name)
	{
		if (! is_string($name) || ! isset($this->graph[$name])) {
			return false;
		}

		return true;
	}

	public function resolve(array $modules)
	{
		
		$results = array();
		foreach ($modules as $name) {
			if (! is_string($name) || empty($name)) {
				$err = 'module name must be a non empty string';
				throw new InvalidArgumentException($err);
			}
			$this->loadModule($name);
		}

		echo "\n", print_r(array($this->js, $this->css),1), "\n";exit;
	}

	/**
	 * @param	string	$module
	 * @return	string
	 */
	public function makeFileName($module, $type)
	{
		$mode = $this->getMode();
		$name = "$module/$module";
		if ('debug' === $mode) {
			$name .= "-debug";
		}
		else if ('min' === $mode) {
			$name .= "-min";
		}

		if ('css' === $type) {
			$name .= '.css';
		}
		else {
			$name .= '.js';
		}
		
		return $name;
	}

    public function getType(array $module)
    {
        $type = 'js';
        if (isset($module['type']) && 'css' === $module['type']) {
            $type = 'css';
        }

        return $type;
    }

    public function getUseModules(array $module)
    {
        $result = false;
        if (isset($module['use']) && is_array($module['use'])) {
            $result = $module['use'];
        }

        return $result;
    }

    public function getRequireModules(array $module)
    {
        $result = false;
        if (isset($module['requires']) && is_array($module['requires'])) {
            $result = $module['requires'];
        }

        return $result;
    }

	public function addModuleFile($module, $type)
	{
		if ('js' === $type) {
			if (! in_array($module, $this->js, true)) {
				$this->js[] = $module;
			}
		}
		elseif ('css' === $type) {
			if (! in_array($module, $this->css, true)) {
				$this->css[] = $module;
			}
		}

		return $this;
	}

	public function loadModule($name)
	{
		$module = $this->getModule($name);
		if (false === $module) {
			return;
		}

        $type = $this->getType($module);
        $useModules = $this->getUseModules($module);
        if (false !== $useModules) {
            foreach ($useModules as $moduleName) {
                $this->loadModule($moduleName);
            }
            return;
        }

        $requireModules = $this->getRequireModules($module);
        if (false !== $requireModules) {
            foreach ($requireModules as $moduleName) {
            	$this->loadModule($moduleName);
            }

			$this->addModuleFile($name, $type);
            return;
        }

        if (false === $useModules && false === $requireModules) {
			$this->addModuleFile($name, $type);
        }
	
	}
	
	/**
	 * @return	string
	 */
	public function getMode()
	{
		return $this->mode;
	}

	/**
	 * @param	string	$mode
	 * @return	YUI3DependencyResolver
	 */
	public function setMode($mode)
	{
		if (! is_string($mode) || !($mode = trim($mode))) {
			$mode = 'min';
		}

		$mode = strtolower($mode);
		if (! in_array($mode, array('min', 'raw', 'debug'))) {
			$err = "file mode must be -(min|raw|debug) -($mode) not found";
			throw new InvalidArgumentException($err);
		}

		$this->mode = $mode;
		return $this;
	}
}
