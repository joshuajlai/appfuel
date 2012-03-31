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
class AppfuelManifest implements AppfuelManifestInterface
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
	 * @var array
	 */
	protected $import = array(
		'layer' => array(),
		'pkg'   => array(),
	);

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

		if (isset($data['desc'])) {
			$this->setPackageDescription($data['desc']);
		}

		if (! isset($data['relative-path'])) {
			$err = "relative path to the this package's directory must be set";
			throw new InvalidArgumentException($err);
		}
		$this->setRelativePath($data['relative-path']);
	
		if (! isset($data['src']) || ! is_array($data['src'])) {
			$err = 'every package must define a source: no src key found';
			throw new InvalidArgumentException($err);
		}
		$this->initSource($data['src']);

		$this->srcPath = $this->relativePath;
		if (! empty($this->srcDir)) {
			$this->srcPath .= "/{$this->srcDir}";
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
	public function getFiles($type)
	{
		return $this->getSourceFileStack()
					->get($type);
	}

	/**
	 * @return	array
	 */
	public function getImportLayers()
	{
		return $this->import['layer'];
	}

	/**
	 * @return	array
	 */
	public function getImportPackages()
	{
		return $this->import['pkg'];
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

		if (isset($src['import-layer'])) {
			$this->setImport('layer', $src['import-layer']);
		}

		if (isset($src['import-pkg'])) {
			$this->setImport('pkg', $src['import-pkg']);
		}
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
	 * @param	string	$name
	 * @return	null
	 */
	protected function setFilename($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'build file must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->filename = $name;
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
	protected function setImport($type, array $list)
	{
		if ('pkg' !== $type && 'layer' !== $type) {
			$err = "failed to set import: invalid type";
			throw new InvalidArgumentException($err);
		}

		foreach ($list as $vendor => $data) {
			if (! is_string($vendor) || empty($vendor)) {
				$err = 'vendor name must be a non empty string';
				throw new InvalidArgumentException($err);
			}
		}

		$this->import[$type] = $list;
	}

	/**
	 * @return	PackageFileList
	 */
	protected function createFileStack(array $files)
	{
		return new FileStack($files);
	}
}
