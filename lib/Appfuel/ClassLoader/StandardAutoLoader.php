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
namespace Appfuel\ClassLoader;

use Appfuel\Framework\Exception;

/**
 * The standard autoloader is an implementation that implements the technical 
 * interoperability standards for PHP 5.3 namespaces and class names.
 *
 *  Example which loads classes for anything in the lib dir
 *  $classLoader = new StandardAutoloader();
 *  $classLoader->addPath('path/to/lib')
 *  $classLoader->register();
 * 
 */
class StandardAutoLoader implements AutoloaderInterface
{
	/**
	 * Used to parse the namespace into paths
	 * @var NamespaceParserInterface
	 */
	protected $parser = null;

	/**
	 * List of paths to search in
	 * @var array
	 */
	protected $pathList = array();

	/**
	 * Flag used to determine if we should search the include path
	 * @var bool
	 */
	protected $isIncludePath = false;

	/**
	 * @param	NamespaceParserInterface $parser
	 * @return	StandardAutoLoader
	 */
	public function __construct(NamespaceParserInterface $parser = null)
	{
		if (null === $parser) {
			$parser = new NamespaceParser();
		}
		$this->setParser($parser);
	}

	/**
	 * @return	NamespaceParserInterface
	 */
	public function getParser()
	{
		return $this->parser;
	}

	/**
	 * @param	NamespaceParserInterface $parser
	 * @return	StandardAutoLoader
	 */
	public function setParser(NamespaceParserInterface $parser)
	{
		$this->parser = $parser;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isIncludePathEnabled()
	{
		return $this->isIncludePath;
	}

	/**
	 * @return	StandardAutoLoader
	 */
	public function enableIncludePath()
	{
		$this->isIncludePath = true;
		return $this;
	}

	/**
	 * @return	StandardAutoLoader
	 */
	public function disableIncludePath()
	{
		$this->isIncludePath = false;
		return $this;
	}

	/**
	 * @param	string	$namespace	
	 * @param	string	absolute path to namespace
	 * @return	StandardAutoLoader
	 */
	public function addPath($path)
	{
		if (empty($path) || !is_string($path) || !($path = trim($path))) {
			throw new Exception("a path must be a non empty string");
		}

		if (! in_array($path, $this->pathList)) {
			$this->pathList[] = $path;
		}	
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getPaths()
	{
		return $this->pathList;
	}

	/**
	 * @param	bool	$flag
	 * @return	null
	 */
	public function register($flag = false) 
	{
		$prepend = false;
		if (true === $flag) {
			$prepend = true;
		}

		spl_autoload_register(array($this, 'loadClass'), true, $prepend);
	}

	/**
	 * @return null
	 */
	public function unregister()
	{
		spl_autoload_unregister(array($this, 'loadClass'));
	}

	/**
	 * @param	string
	 * @return	null
	 */
	public function loadClass($class)
	{
		$parser = $this->getParser();
		$path   = $parser->parse($class);
		if (false === $path) {
			return null;
		}
				
		foreach ($this->pathList as $root) {
			$file = $root . DIRECTORY_SEPARATOR . $path;
			if (is_file($file)) {
				require $file;
				return true;
			}		
		}

		if ($this->isIncludePathEnabled() && 
			$file = stream_resolve_include_path($path)) {
			require $file;
			return true;
		}
	}
}
