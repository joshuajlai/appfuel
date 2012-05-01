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
 */
class AppfuelLayer implements ResourceLayerInterface 
{
	/**
	 * @var string
	 */
	protected $name = null;
	
	/**
	 * @var VendorInterface
	 */
	protected $vendor = null;

	/**
	 * Name of the file used when layer is rolled up. 
	 * @var string
	 */
	protected $filename = null;

	/**
	 * @var array
	 */
	protected $pkgList = array();

	/**
	 * @param	array $data	
	 * @return	PackageManifest
	 */
	public function __construct($name, VendorInterface $vendor)
	{
		$this->setLayerName($name);
		$this->setVendor($vendor);
	}

	/**
	 * @return	string
	 */
	public function getLayerName()
	{
		return $this->name;
	}

	/**
	 * @param	string	$name
	 * @return	ResourceLayer
	 */
	public function setLayerName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'layer name must be a none empty string';
			throw new InvalidArgumentException($err);
		}
		$this->name = $name;
	}

	/**
	 * @return	VendorInterface
	 */
	public function getVendor()
	{
		return $this->vendor;
	}

	/**
	 * @param	string	$name
	 * @return	ResourceLayer
	 */
	public function setVendor(VendorInterface $vendor)
	{
		$this->vendor = $vendor;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getFilename()
	{
		return $this->filename;
	}

	/**
	 * @return	array
	 */
	public function getPackages()
	{
		return $this->pkgList;
	}

	public function setPackages(array $list)
	{
		$result = array();
		$vendorName = $this->getVendor()
						   ->getVendorName();

		$result = array();
		foreach ($list as $name) {
			$result[] = new PkgName($name, $vendorName);
		}
		$this->pkgList = $result;
		return	$this;
	}

	/**
	 * @param	string	$name
	 * @return	Yui3Layer
	 */
	public function setFilename($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'filename must be non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->filename = $name;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isFilename()
	{
		return is_string($this->filename) && ! empty($this->filename);
	}

    /**
     * @return bool
     */
    public function isCss()
    {
        return $this->getFileStack()
                    ->isType('css');
    }

    /**
     * @return bool
     */
    public function isJs()
    {
        return $this->getFileStack()
                    ->isType('js');
    }

	/**
	 * @return	string
	 */
	public function getCssFile()
	{
		return $this->getBuildFile() . '.css';
	}

	/**
	 * @return	string
	 */
	public function getJsFile()
	{
		return $this->getBuildFile() . '.js';
	}

	/**
	 * @return	YuiFileStackInterface
	 */
	public function getFileStack()
	{
		return $this->stack;
	}

	/**
	 * @param	FileStackInterface $stack
	 * @return	Yui3Layer
	 */
	public function setFileStack(FileStackInterface $stack)
	{
		$this->stack = $stack;
		return $this;
	}
	
	/**
	 * @return	array
	 */
	public function getAllCssSourcePaths()
	{
		return $this->getSourcePaths('css');
	}

	/**
	 * @return	array
	 */
	public function getAllJsSourcePaths()
	{
		return $this->getSourcePaths('js');
	}

	/**
	 * @return	array
	 */
	protected function getSourcePaths($type)
	{
		$vendor  = $this->getVendor();
		$srcPath = $vendor->getPackagePath();
		$list    = $this->getFileStack()
						->get($type);

		if (empty($list)) {
			return array();
		}
		
		return $list;
	}

	public function getBuildDir()
	{
		$vendor   = $this->getVendor();
		$name     = $vendor->getVendorName(); 
		$version  = $vendor->getVersion();
		return "build/$name/$version";
	}

	/**
	 * @return	string
	 */
	public function getBuildFile()
	{
		if (! $this->isFilename()) {
			$err = "can not get layer file path before setting the filename";
			throw new DomainException($err);
		}

		$dir = $this->getBuildDir();
		return "$dir/{$this->getFilename()}";
	}
}
