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
namespace Appfuel\View\Html\Resource;

use LogicException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileReader,
	Appfuel\Filesystem\FileReaderInterface;

/**
 * 
 */
class ResourcePackage implements ResourcePackageInterface
{
	/**
	 * Absolute path to the root of the application
	 * @var string
	 */
	protected $basePath = null;

	/**
	 * Relative path from the base path to the package dir
	 * @var string
	 */
	protected $pkgPath = null;

	/**
	 * Relative path from the base path to package files, this includes the
	 * package path. 
	 * @var string
	 */
	protected $filePath = null;

	/**
	 * Relative path from the base path to package test files, this includes
	 * the package path.
	 * @var string
	 */
	protected $testPath = null;
	
	/**
	 * @var ResourceVendorInterface
	 */
	protected $vendor = null;

	/**
	 * @var ManifestInterface
	 */
	protected $manifest = null;

	/**
	 * @var FileReaderInterface
	 */
	protected $reader = null;

	/**
	 * @param	string	$dir 
	 * @return	ResourcePackage
	 */
	public function __construct(ResourceVendorInterface $vendor,
								PackageManifestInterface $manifest,
								FileReaderInterface $reader)
	{
		$this->vendor   = $vendor;
		$this->manifest = $manifest;
		$this->reader   = $reader;
		$this->basePath = $reader->getFileFinder()
								 ->getBasePath();


		$fileDir = $manifest->getFileDir();
		$testDir = $manifest->getTestDir();
		$pkgPath = $vendor->getPackagePath();
		$pkgDir  = $manifest->getPackageDir();

		$this->pkgPath  = "$pkgPath/$pkgDir";
		$this->filePath = "{$this->pkgPath}/{$fileDir}";
		$this->testPath = "{$this->pkgPath}/{$testDir}";
	}

	/**
	 * @return	ResourceVendorInterface
	 */
	public function getVendor()
	{
		return $this->vendor;
	}

	/**
	 * @return	PackageManifestInterface
	 */
	public function getManifest()
	{
		return $this->manifest;
	}

	/**
	 * @return	FileReaderInterface
	 */
	public function getFileReader()
	{
		return $this->reader;
	}

	/**
	 * @param	string	$path	relative path to package
	 * @return	ResourcePackage
	 */
	public function load($path)
	{
		if (! is_string($path) || empty($path)) {
			$err = 'path to package must be a non empty string';
			throw new InvalidArgumentException($err);
		}
	
		$resourceDir = $this->getResourceDir();
		$pkgPath = "$resourceDir/$path";
		$reader = $this->createFileReader("$resourceDir/$path");
		$this->setFileReader($reader);
		$str = $reader->getContentAsString("manifest.json");
		if (false === $str) {
			$err = "manifest.json could not be found at -($pkgPath)";
			throw new LogicException($err);
		}

		$data = json_decode($str, true);
		if (! is_array($data)) {
			$err = "manifest.json cound be be decoded -($pkgPath)";
			throw new LogicException($err);
		}

		$manifest = $this->createPackageManifest($data);
		$this->setManifest($manifest);
	}

	/**
	 * @return	array
	 */
	public function getDependencies()
	{
		return $this->getManifest()
					->getDependencies();
	}

	/**
	 * @return	string
	 */
	public function getBasePath()
	{
		return $this->basePath;
	}

	/**
	 * @return	string
	 */
	public function getPackagePath()
	{
		return $this->pkgPath;
	}

	/**
	 * @return	return	string
	 */
	public function getFilePath()
	{
		return $this->filePath;
	}

	/**
	 * @return	return	string
	 */
	public function getTestPath()
	{
		return $this->testPath;
	}

	/**
	 * @param	string	$type
	 * @param	bool	$isAbsolute
	 * @return	array
	 */
	public function getPaths($type, $isAbsolute = false)
	{
		$manifest = $this->getManifest();
		$files	  = $manifest->getFiles($type);
		if (false === $files) {
			return false;
		}

		$base = '';
		if (true === $isAbsolute) {
			$base = $this->getBasePath() . '/';
		}

		$path = $this->getFilePath();  
		$max  = count($files);
		for ($i=0; $i < $max; $i++) {
			$files[$i] = "{$base}{$path}/{$files[$i]}";
		}
		
		return $files;
	}

	/**
	 * @param	string	$type
	 * @param	bool	$isAbsolute
	 * @param	array	$exclude
	 * @return	array
	 */
	public function getTestPaths($type, $isAbsolute = false) 
	{
		$manifest = $this->getManifest();
		$files	  = $manifest->getTestFiles($type);

		if (false === $files) {
			return false;
		}

		$base = '';
		if (true === $isAbsolute) {
			$base = $this->getBasePath() . '/';
		}

		$path = $this->getFilePath();  
		$max  = count($files);
		for ($i=0; $i < $max; $i++) {
			$files[$i] = "{$base}{$path}/{$files[$i]}";
		}
	
		return $files;
	}

	public function getFileData($path)
	{
		$manifest = $this->getManifest();
		$reader   = $this->getFileReader();

		$pkgPath  = $vendor->getPackagePath();
		$pkgDir   = $manifest->getFileDir();

		$file = "{$vendor->getPackagePath()}/{$manfiest->getFileDir()}/$path";
	}

	/**
	 * @param	string	$type
	 * @param	array	$exclude
	 * @return	string
	 */
	public function getData($type, array $callback = null, $sep = PHP_EOL)
	{
		if (null === $sep || ! is_string($sep)) {
			$sep = PHP_EOL;
		}

		$files = $this->getPaths($type, true);
		if (false === $files) {
			return false;
		}

		$data = '';
		$isRelative = false;
		$reader = $this->getFileReader();
		foreach ($files as $path) {
			$tmp = $reader->getContent($path, $isRelative);
			if (false === $tmp) {
				$err = "could not read file into a string at -($path)";
				throw new RunTimeException($err);
			}
			
			if (is_callable($callback)) {
				$tmp = call_user_func($callback, $path, $tmp);
			}

			$data .= $tmp . $sep;
		}

		return $data;	
	}
}
