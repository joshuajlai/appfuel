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

use InvalidArgumentException;

/**
 * A value object used to describe the manifest.json 
 */
class PackageManifest implements PackageManifestInterface
{
	/**
	 * @var string
	 */
	protected $name = null;

	/**
	 * @var string
	 */
	protected $desc = null;

	/**
	 * @var string
	 */
	protected $packageDir = null;

	/**
	 * Relative path from the package dir to the package files
	 * @var string
	 */
	protected $filePath = 'src';

	/**
	 * @var PackageFileListInterface
	 */
	protected $files = null; 

	/**
	 * Relative path from the package dir to the test files
	 * @var string 
	 */
	protected $testPath = 'tests';
	
	/**
	 * @var PackageFileListInterface
	 */
	protected $tests = null;

	/**
	 * @var array
	 */
	protected $depends = array();

	/**
	 * @param	string|ViewInterface $view	
	 * @param	string				 $htmlDocFile	path to phtml file
	 * @param	HtmlTagFactory		 $factory
	 * @return	HtmlPage
	 */
	public function __construct(array $data)
	{
		if (! isset($data['name'])) {
			$err = 'package name not found an must exist';
			throw new InvalidArgumentException($err);
		}
		$this->setPackageName($data['name']);

		if (isset($data['desc'])) {
			$this->setPackageDescription($data['desc']);
		}
	
		$pkgDir = isset($data['dir']) ? $data['dir'] : $this->getPackageName();	
		$this->setPackageDirectory($pkgDir);
		
		if (! isset($data['files'])) {
			$this->setResourceFiles($this->createPackageFileList());
		}
		else if (is_array($data['files'])) {
			$this->setResourceFiles($data['files']);
		}
		else {
			$err = 'resource must be an associative array';
			throw new InvalidArgumentException($err);
		}

		if (! isset($data['tests'])) {
			$this->setResourceTests($this->createPackageFileList());
		}
		else if (is_array($data['tests'])) {
			$this->setResourceTests($data['tests']);
		}
		else {
			$err = 'tests must be an associative array';
			throw new InvalidArgumentException($err);

		}

		if (isset($data['depends'])) {
			$this->setDependencies($data['depends']);
		}
	}

	/**
	 * @return	string
	 */
	public function getPackageName()
	{
		return $this->name;
	}

	/**
	 * @return	string
	 */
	public function getPackageDescription()
	{
		return $this->desc;
	}

	/**
	 * @return	string
	 */
	public function getPackageDirectory()
	{
		return $this->pkgDir;
	}
	
	/**
	 * @return	string
	 */
	public function getFilePath()
	{
		return $this->filePath;
	}

	/**
	 * @return	array
	 */
	public function getFileTypes()
	{
		return $this->getResourceFiles()
					->getTypes();
	}

	/**
	 * @return	string
	 */
	public function getAllFiles()
	{
		return $this->getResourceFiles()
					->getAll();
	}

	/**
	 * @params	string $type 
	 * @return	array|false
	 */
	public function getFiles($type)
	{
		return $this->getResourceFiles()
					->get($type);
	}

	/**
	 * @return	string
	 */
	public function getTestPath()
	{
		return $this->testPath;
	}

	/**
	 * @return	array
	 */
	public function getTestFileTypes()
	{
		return $this->getResourceTestFiles()
					->getTypes();
	}

	/**
	 * @return	array
	 */
	public function getAllTestFiles()
	{
		return $this->getResourceTestFiles()
					->getAll();
	}

	/**
	 * @param	string	$type
	 * @return	array|false
	 */
	public function getTestFiles($type)
	{
		return $this->getResourceTestFiles()
					->get($type);
	}

	/**
	 * @return	array
	 */
	public function getDependencies()
	{
		return $this->depends;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setPackageName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'package name must be a none empty string';
			throw new InvalidArgumentException($err);
		}
		$this->name = $name;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setPackageDescription($desc)
	{
		if (! is_string($desc)) {
			$err = 'package description must be a string';
			throw new InvalidArgumentException($err);
		}

		$this->desc = $desc;
	}

	/**
	 * @param	string	$dir
	 * @return	null
	 */
	protected function setPackageDirectory($dir)
	{
		if (! is_string($dir)) {
			$err = 'package directory must be a string';
			throw new InvalidArgumentException($err);
		}

		$this->pkgDir = $dir;
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */	
	protected function setResourceFiles($files)
	{
		if ($files instanceof PackageFileListInterface) {
			$this->files = $files;
			return;
		}
		else if (! is_array($files)) {
			$err  = 'files must be an array or an object that implments ';
			$err .= 'Appfuel\View\Html\Resource\PackageFileListInterface';
			throw new InvalidArgumentException($err);
		}

		if (isset($files['path'])) {
			$path = $files['path'];
			if (! is_string($path)) {
				$err = 'path to resource files must be a string';
				throw new InvalidArgumentException($err);
			}
			$this->setFilePath($path);
			unset($files['path']);
		}
		else {
			$this->setFilePath('src');
		}

		$list = $this->createPackageFileList();
		$list->set($files);
		$this->files = $list;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setFilePath($dir)
	{
		if (! is_string($dir)) {
			$err = 'package file dir must be a string';
			throw new InvalidArgumentException($err);
		}
		$this->filePath = $dir;
	}

	/**
	 * @return	PackageFileListInterface
	 */
	protected function getResourceFiles()
	{
		return $this->files;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setTestPath($dir)
	{
		if (! is_string($dir)) {
			$err = 'package dir must be a string';
			throw new InvalidArgumentException($err);
		}
		$this->testPath = $dir;
	}
		
	/**
	 * @return	PackageFileListInterface
	 */
	protected function getResourceTestFiles()
	{
		return $this->tests;
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */	
	protected function setResourceTests($files)
	{
		if ($files instanceof PackageFileListInterface) {
			$this->tests = $files;
			return;
		}
		else if (! is_array($files)) {
			$err  = 'tests must be an array or an object that implements ';
			$err .= 'Appfuel\View\Html\Resource\PackageFileListInterface';
			throw new InvalidArgumentException($err);
		}

		if (isset($files['path'])) {
			$path = $files['path'];
			if (! is_string($path)) {
				$err = 'path to test files must be a string';
				throw new InvalidArgumentException($err);
			}
			$this->setTestPath($path);
			unset($files['path']);
		}
		else {
			$this->setTestPath('tests');
		}

		$list = $this->createPackageFileList();
		$list->set($files);
		$this->tests = $list;
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */
	protected function setDependencies(array $list)
	{
		if ($list !== array_values($list)) {
			$err = 'dependency list must be an indexed array of strings';
			throw new InvalidArgumentException($err);
		}

		foreach ($list as $package) {
			if (! is_string($package) || empty($package)) {
				$err = 'package path must be a non empty string';
				throw new InvalidArgumentException($err);
			}
		}

		$this->depends = $list;
	}

	/**
	 * @return	PackageFileList
	 */
	protected function createPackageFileList()
	{
		return new PackageFileList();
	}
}
