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
namespace Appfuel\Kernal\Dependency;

use Appfuel\Framework\Exception;

/**
 * The Dependency loader will iterate through the namespace and file list. 
 * Each namespace is resolved and loaded with a require call. Each file is
 * is resolved and loaded with a require_once call.
 */
class DependencyLoader implements DependencyLoaderInterface
{
	/**
	 * List of dependency objects to be loaded
	 * @var	array
	 */
	protected $dependencies = array();

	/**
	 * Error message used to indicate why loading failed
	 * @var string
	 */
	protected $error = null;

	/**
	 * @param	string	$rootPath
	 * @return	ClassDependency
	 */
	public function __construct(array $list = null)
	{

	}

	/**
	 * @return	string
	 */
	public function getDependencies()
	{
		return $this->dependencies;
	}

	/**
	 * @param	string	
	 * @return	ClassDependency
	 */
	public function addDepenency(ClassDependencyInterface $dependency)
	{
		$this->dependencies[] = $dependency;
		return $this;
	}

	public function loadDependency(ClassDependencyInterface $dependency)
	{
		$list = $dependency->getNamespaces();
		$root = $dependency->getRootPath();
		if (! file_exists($root)) {
			$this->setError("namespace root path does not exist");
			return false;	
		}
		$dsep = DIRECTORY_SEPARATOR;
		$nsep = '\\';
		foreach ($list as $class) {
			/* remove leading namespace char */
			if ($nsep === $class[0]) {
				$class = substr($class, 1);
			}
			
			$pos = strrpos($class, 0 $nsep);
			if (false !== $pos) {
				$namespace = substr($class, 0, $pos);
				$classname = substr($class, $pos + 1);
				$resolved  = str_replace($nsep, $dsep, $namespace) . $dsep .
							 str_replace('_', $dsep, $classname);


			}
			/* parse pear naming scheme */
			else {
				$resolved = str_replace('_', $dsep, $class);
			}
			$resolved .= '.php';
		}

	}

	/**
	 * @return	bool
	 */
	public function isError()
	{
		return strlen($this->error) > 0;
	}

	/**
	 * @return	string | null if not set
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @param	string	$err
	 * @return	null
	 */
	protected function setError($err)
	{
		$this->error = $err;
	}
}
