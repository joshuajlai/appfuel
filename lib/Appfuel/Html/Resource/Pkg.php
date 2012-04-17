<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Html\Resource;

use DomainException,
	InvalidArgumentException;

/**
 * A value object used to describe the manifest.json in the pkg directory
 */
class Pkg implements PkgInterface
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
	 * Used to validate that the type of package is the expected one
	 * @var string
	 */
	protected $validType = 'pkg';

	/**
	 * @var string
	 */
	protected $desc = null;

	/**
	 * Relative path from the package directory to the root of this package
	 * @var string
	 */	
	protected $path = null;

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
	protected $required = array();

	/**
	 * @param	array $data	
	 * @return	PackageManifest
	 */
	public function __construct(array $data, $vendor = null)
	{
		if (! isset($data['name'])) {
			$err = 'package name not found an must exist';
			throw new DomainException($err);
		}
		$this->setName($data['name']);

		if (! isset($data['type'])) {
			$err = 'package type must be defined but was not found';
			throw new DomainException($err);
		}
		$this->setType($data['type']);

		if (isset($data['desc'])) {
			$this->setDescription($data['desc']);
		}

		if (isset($data['path'])) {
			$this->setPath($data['path']);
		}
		else {
			$this->setPath($data['name']);
		}
	
		if (isset($data['src']) && is_array($data['src'])) {
			$this->initSource($data['src']);
		}

		$this->srcPath = $this->path;
		if (! empty($this->srcDir)) {
			$this->srcPath .= "/{$this->srcDir}";
		}

		if (isset($data['require'])) {
			$this->setRequiredPackages($data['require'], $vendor);
		}
	}

	/**
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return	string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param	string	$type
	 * @return	bool
	 */
	public function isType($type)
	{
		return ($type === $this->getType()) ? true : false;
	}

	/**
	 * @return	string
	 */
	public function getDescription()
	{
		return $this->desc;
	}

	/**
	 * @return	string
	 */
	public function getPath()
	{
		return $this->path;
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
     * @return  bool
     */
    public function isJsFiles()
    {
        return $this->getSourceFileStack()
                    ->isType('js');
    }

    /**
     * @param   string  $path   
     * @return  array
     */    
    public function getJsFiles($path = null)
    {
        return $this->getFiles('js', $path);    
    }


    /**
     * @return  bool
     */
    public function isCssFiles()
    {
        return $this->getSourceFileStack()
                    ->isType('css');
    }

    /**
     * @param   string  $path   
     * @return  array
     */    
    public function getCssFiles($path = null)
    {
        return $this->getFiles('css', $path);    
    }

    /**
     * @return  bool     
     */
    public function isAssetFiles()    
    { 
        return $this->getSourceFileStack()
                    ->isType('assets'); 
    }

    /**
     * @param   string  $path
     * @return  array
     */    
    public function getAssetFiles($path = null)
    {
        return $this->getFiles('assets', $path);
    }

    /**
     * @param   string  $path
     * @return  string
     */
    public function getAssetDir($path = null)
    {
		$srcPath = $this->getSourcePath();
		if (is_string($path) && ! empty($path)) {
			$srcPath = "$path/$srcPath";
		}

        return "$srcPath/assets";	
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
		return ! empty($this->required);
	}

	/**
	 * @return	array
	 */
	public function getRequiredPackages()
	{
		return $this->required;
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
	protected function setName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'package name must be a none empty string';
			throw new InvalidArgumentException($err);
		}
		$this->name = $name;
	}

	/**
	 * @return	string
	 */
	protected function getValidType()
	{
		return $this->validType;	
	}

	/**
	 * @param	string	$type
	 * @return	null
	 */
	protected function setValidType($type)
	{
		if (! is_string($type)) {
			$err = 'valid type must be a string';
			throw new InvalidArgumentException($err);
		}

		$this->validType = $type;
	}

	/**
	 * @param	string	$type
	 * @return	null
	 */
	protected function setType($type)
	{
		if (! is_string($type) || empty($type)) {
			$err = 'package type must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if ($this->getValidType() !== $type) {
			$err  = "pkg type must be -({$this->getValidType()}) -($type) ";
			$err .= "given";
			throw new DomainException($err);
		}

		$this->type = $type;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setDescription($desc)
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
	protected function setPath($path)
	{
		if (! is_string($path) || empty($path)) {
			$err = "relative path must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->path = $path;
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
	protected function setRequiredPackages(array $list, $vendor = null)
	{
		$names = array();
		foreach ($list as $str) {
			if (false === strpos($str, '.')) {
				$str = "pkg.$str";
			}
			$names[] = new PkgName($str, $vendor);
		}	
		$this->required = $names;
	}

	/**
	 * @return	PackageFileList
	 */
	protected function createFileStack(array $files)
	{
		return new FileStack($files);
	}
}
