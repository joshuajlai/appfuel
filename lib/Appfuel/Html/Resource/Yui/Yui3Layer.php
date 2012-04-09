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
namespace Appfuel\Html\Resource\Yui;

use DomainException,
	InvalidArgumentException,
	Appfuel\Html\Resource\PkgName,
	Appfuel\Html\Resource\VendorInterface,
	Appfuel\Html\Resource\FileStackInterface;

/**
 */
class Yui3Layer 
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
     * @return  array
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
        return  $this;
    }

	/**
	 * @return	bool
	 */
	public function isFilename()
	{
		return is_string($this->filename) && ! empty($this->filename);
	}

	/**
	 * @return	string
	 */
	public function getCssFilePath()
	{
		return $this->getFilePath() . '.css';
	}

	/**
	 * @return	string
	 */
	public function getJsFilePath()
	{
		return $this->getFilePath() . '.js';
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
		if (! $stack instanceof Yui3FileStackInterface) {
			$err  = 'yui3 layer requires a file stack that implements ';
			$err .= __NAMESPACE__ . '\Yui3FileStackInterface';
			throw new DomainException($err);
		}

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

		$result = array();
		foreach ($list as $name) {
			$result[] = "$srcPath/$name/$name.$type";
		}

		return $result;
	}

    public function getBuildDir()
    {
        $vendor   = $this->getVendor();
        $name     = $vendor->getVendorName();
        $version  = $vendor->getVersion();
        return "build/$name/$version";
    }

    /**
     * @return  string
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
