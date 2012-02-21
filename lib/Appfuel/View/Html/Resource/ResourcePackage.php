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
	 * @param	string	$type
	 * @param	bool	$isAbsolute
	 * @return	array
	 */
	public function getFilePaths($type, $isAbsolute = false, $isTest = false)
	{
		$manifest = $this->getManifest();
		$vendor   = $this->getVendor();
		$finder   = $this->getFileReader()
						 ->getFileFinder();

		if (false === $isTest) {
			$files = $manifest->getFiles($type);
		}
		else {
			$files = $manifest->getTestFiles($type);
		}

		if (false === $files) {
			return false;
		}
			
		$basePath = '';
		if (true === $isAbsolute) {
			$basePath = $finder->getBasePath() . '/';
		}

		$pkgPath  = $vendor->getPackagePath();
		$pkgDir   = $manifest->getPackageDir();
			
		$max = count($files);
		for ($i=0; $i < $max; $i++) {
			$files[$i] = "{$basePath}{$pkgPath}/{$pkgDir}/{$files[$i]}";
		}	
		
		return $files;
	}

	/**
	 * @param	string	$type
	 * @param	array	$exclude
	 * @return	string
	 */
	public function getData($type, $callback = null, $sep = PHP_EOL)
	{
		if (null === $sep || ! is_string($sep)) {
			$sep = PHP_EOL;
		}

		$files = $this->getFilePaths($type, true);
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
