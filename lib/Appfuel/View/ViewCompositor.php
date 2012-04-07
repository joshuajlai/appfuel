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
namespace Appfuel\View;

use DomainException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\Html\Resource\PkgName,
	Appfuel\Html\Resource\PkgNameInterface,
	Appfuel\Html\Resource\AppViewManifest,
	Appfuel\Html\Resource\ResourceTreeManager,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileFinderInterface;

/**
 */
class ViewCompositor implements ViewCompositorInterface
{
    /**
	 * List of compositors to cache for reuse
     * @var array
     */
    static private $fileCompositor = array();

	/**
	 * @var FileFinder
	 */
	static private $finder = null;

	
	/**
	 * @param	string	$file	
	 * @param	string	$data
	 * @return	string
	 */
	static public function composeFile($file, array $data)
	{
		if (! is_string($file) || empty($file)) {
			$err = 'file path must be a non empty string';
			throw new InvalidArgumentException($err);
		}
		$finder = self::loadFileFinder();

		$absolute = $finder->getPath($file);
		if (! $finder->fileExists($absolute, false)) {
			$err = "template file not found at -($absolute)";
			throw new DomainException($err, 404);
		}

		$compositor = self::loadFileCompositor();
		return $compositor->compose($absolute, $data);		
	}

	/**
	 * @param	string	$file	
	 * @param	string	$data
	 * @return	string
	 */
	static public function composePackage($name, array $data, $isInit = false)
	{
		if (is_string($name)) {
			$name = new PkgName($name);
		}
		else if (! $name instanceof PkgNameInterface) {
			$err  = 'package name must be a string of an object that ';
			$err .= 'implements Appfuel\Html\Resource\PkgNameInterface';
			throw new InvalidArgumentException($err);
		}

		$pkg = ResourceTreeManager::getPkg($name);
		if (! $pkg) {
			$err = "could not compose pkg -({$name->getName()}) not found";
			throw new DomainException($err);
		}
		$vPath = ResourceTreeManager::getVendorPath($name->getVendor());
		$path  = "$vPath/{$pkg->getMarkupFile()}";

		$finder = self::loadFileFinder();
		$absolute = $finder->getPath($path);
		if (! $finder->fileExists($absolute, false)) {
			$err = "template file not found at -($absolute)";
			throw new DomainException($err, 404);
		}

		$compositor = self::loadFileCompositor();
		$view = $compositor->compose($absolute, $data);
		
		if ($pkg->isJsInitFile()) {
			$path = "$vPath/{$pkg->getJsInitFile()}";
			$absolute = $finder->getPath($path);
			if (! $finder->fileExists($absolute, false)) {
				$err = "template file not found at -($absolute)";
				throw new DomainException($err, 404);
			}
			$init = $compositor->compose($absolute, $data);
			return array($view, $init);
		}

		return $view;
	}
	/**
	 * @param	array	$data	
	 * @param	int		$options
	 * @return	string
	 */
	static public function composeJson(array $data, $options = 0)
	{
		if (0 !== $options && ! is_int($options) || $options < 0) {
			$err = 'json options must be a positive integer';
			throw new InvalidArgumentException($err);
		}

		return json_encode($data, $options);
	}

	/**
	 * @param	array	$data
	 * @return	string
	 */
	static public function composeCsv(array $data) 
	{
		$stream = fopen('php://tmp', 'r+');

		fputcsv($stream, $data, ',', '"');
		rewind($stream);
		$result = fgets($stream);

		fclose($stream);
		
		return $result;
	}

	/**
	 * Removes only the values from the list and converts them into a string
	 * where each item is separated by $sep
	 * 
	 * @param	array	$data
	 * @param	string	$sep
	 * @return	string
	 */
	static public function composeList(array $data, $sep = ' ')
	{
		if (empty($data)) {
			return '';
		}

		return  self::composeArray(array_values($data), $sep);
	}
	
	/**
	 * @param	string	$str
	 * @return	string
	 */
	static public function composeString($str)
	{
		if (is_scalar($str) || 
			(is_object($str) && is_callable(array($str, '__toString')))) {
			return (string) $str;
		}

		return '';
	}

	/**
	 * @param	array	$list
	 * @param	string	$sep
	 * @return	string
	 */
	static public function composeArray(array $list, $sep = ' ')
	{
		if (! is_string($sep)) {
			$err = 'list separator must be a string';
			throw new InvalidArgumentException($err);
		}

		$result = '';
		foreach ($list as $item) {
			if (is_array($item)) {
				$result .= self::composeArray($item, $sep) . $sep;
			}
			else {
				$result .= self::composeString($item) . $sep;
			}
		}

		return trim($result, "$sep");
	}

	/**
	 * File finder with a relative path to the resource directory
	 * @return	FileFinder
	 */
	public function createFinder()
	{
		return new FileFinder('resource');
	}

	/**
	 * @return	TemplateCompositorInterface
	 */
	static public function getFileCompositor()
	{
		return self::$fileCompositor;
	}

	/**
	 * @param	FileCompositorInterface $comp
	 * @return	null
	 */
	static public function setFileCompositor(FileCompositorInterface $comp)
	{
		self::$fileCompositor = $comp;
	}
		
	/**
	 * @return	FileCompositorInterface
	 */
	static public function loadFileCompositor()
	{
		if (self::isFileCompositor()) {
			return self::getFileCompositor();
		}

		$compositor = self::createFileCompositor();
		self::setFileCompositor($compositor);
		return $compositor;
	}

	/**
	 * @return	bool
	 */
	static public function isFileCompositor()
	{
		return self::$fileCompositor instanceof FileCompositorInterface;
	}

	/**
	 * @return	null
	 */
	static public function createFileCompositor()
	{
		return new FileCompositor();
	}
	
	/**
	 * @return	FileFinderInterface
	 */
	static public function getFileFinder()
	{
		return self::$finder;
	}

	/**
	 * @param	FileFinderInterface $finder
	 * @return	null
	 */
	static public function setFileFinder(FileFinderInterface $finder)
	{
		self::$finder = $finder;
	}

	/**
	 * @return	bool
	 */
	static public function isFileFinder()
	{
		return self::$finder instanceof FileFinderInterface;
	}

	/**
	 * @param	string	$path	
	 * @param	bool	$isBase
	 * @return	FileFinderInterface
	 */
	static public function createFileFinder($path = null, $isBase = true)
	{
		if (null === $path) {
			$path = 'resource';
		}

		if (! is_string($path) || empty($path)) {
			$err = 'when a path is given it needs to be a non empty string';
			throw new InvalidArgumentException($path);
		}

		$isBasePath = true;
		if (false === $isBase) {
			$isBasePath = false;
		}

		return new FileFinder($path, $isBasePath);
	}

	/**
	 * @return	FileFinderInterface
	 */
	static public function loadFileFinder()
	{
		if (self::isFileFinder()) {
			return self::getFileFinder();
		}

		$finder = self::createFileFinder();
		self::setFileFinder($finder);
		return $finder;
	}
}
