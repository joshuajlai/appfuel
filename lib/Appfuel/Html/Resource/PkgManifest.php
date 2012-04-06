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
namespace Appfuel\Html\Resource;

use InvalidArgumentException;

/**
 * A value object used to describe the manifest.json in the package directory
 */
class PkgManifest implements PkgManifestInterface
{
	/**
	 * @var string
	 */
	protected $name = null;
	
	/**
	 * @var string
	 */
	protected $type = null;

	/**
	 * @var string
	 */
	protected $desc = null;

	/**
	 * Relative path from the package directory to the root of this package
	 * @var string
	 */	
	protected $relativePath = null;

	/**
	 * Relative path from the package dir to the package files
	 * @var string
	 */
	protected $srcDir = 'src';

	/**
	 * @var FileStackInterface
	 */
	protected $files = null; 

	/**
	 * List of appfuel packages to import
	 * @var array
	 */
	protected $requiredPkgs = array();

	/**
	 * @param	array $data	
	 * @return	PackageManifest
	 */
	public function __construct(array $data)
	{
		if (! isset($data['name'])) {
			$err = 'package name not found an must exist';
			throw new InvalidArgumentException($err);
		}
		$this->setPackageName($data['name']);

		if (! isset($data['type'])) {
			$err = 'package type must be defined but was not found';
			throw new InvalidArgumentException($err);
		}
		$this->setPackageType($data['type']);

		if (isset($data['desc'])) {
			$this->setPackageDescription($data['desc']);
		}

		if (isset($data['relative-path'])) {
			$this->setRelativePath($data['relative-path']);
		}
	
		if (isset($data['src']) && is_array($data['src'])) {
			$this->initSource($data['src']);
		}

		$this->srcPath = $this->relativePath;
		if (! empty($this->srcDir)) {
			$this->srcPath .= "/{$this->srcDir}";
		}

		if (isset($data['required'])) {
			$this->setRequiredPackages($data['required']);
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
	public function getPackageType()
	{
		return $this->type;
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
	public function getRelativePath()
	{
		return $this->relativePath;
	}

	/**
	 * @return	string
	 */
	public function getSourcePath()
	{
		return $this->srcPath;
	}

	/**
	 * @return	string
	 */
	public function getSourceDirectory()
	{
		return $this->srcDir;
	}

	/**
	 * @return	array
	 */
	public function getFileTypes()
	{
		return $this->getSourceFileStack()
					->getTypes();
	}

	/**
	 * @return	string
	 */
	public function getAllFiles()
	{
		return $this->getSourceFileStack()
					->getAll();
	}

	/**
	 * @params	string $type 
	 * @return	array|false
	 */
	public function getFiles($type, $path = null)
	{
		$srcPath = $this->getSourcePath();
		if (is_string($path) && ! empty($path)) {
			$srcPath = "$path/$srcPath";
		}
		
		return $this->getSourceFileStack()
					->get($type, $srcPath);
	}

	/**
	 * @return	bool
	 */
	public function isRequiredPackages()
	{
		return ! empty($this->requiredPkgs);
	}

	/**
	 * @return	array
	 */
	public function getRequiredPackages()
	{
		return $this->requiredPkgs;
	}

	/**
	 * @param	array	$src
	 * @return	null
	 */
	protected function initSource(array $src)
	{
		if (isset($src['dir'])) {
			$this->setSourceDirectory($src['dir']);
		}	

		if (! isset($src['files'])) {
			$err = 'every package must define its source files: none found';
			throw new InvalidArgumentException($err);
		}
		$this->setSourceFileStack($src['files']);
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
	 * @param	string	$type
	 * @return	null
	 */
	protected function setPackageType($type)
	{
		if (! is_string($type) || empty($type)) {
			$err = 'package type must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->type = $type;
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
	 * @param	string	
	 * @return	null
	 */
	protected function setRelativePath($path)
	{
		if (! is_string($path) || empty($path)) {
			$err = "relative path must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->relativePath = $path;
	}

	/**
	 * @return	FileStack
	 */
	protected function getSourceFileStack()
	{
		return $this->files;
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */	
	protected function setSourceFileStack($files)
	{
		if ($files instanceof FileStackInterface) {
			$this->files = $files;
			return;
		}
		else if (! is_array($files)) {
			$err  = 'files must be an array or an object that implments ';
			$err .= 'Appfuel\Html\Resource\FileStackInterface';
			throw new InvalidArgumentException($err);
		}

		$list = $this->createFileStack($files);
		$this->files = $list;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setSourceDirectory($dir)
	{
		if (! is_string($dir)) {
			$err = 'package source directory must be a string';
			throw new InvalidArgumentException($err);
		}
		$this->srcDir = $dir;
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */
	protected function setRequiredPackages($list)
	{
		$this->requiredPkgs = $list;
	}

	/**
	 * @return	PackageFileList
	 */
	protected function createFileStack(array $files)
	{
		return new FileStack($files);
	}
}
