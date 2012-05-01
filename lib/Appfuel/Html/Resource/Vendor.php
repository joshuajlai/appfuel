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

use InvalidArgumentException;

/**
 * Value object used to describe the vendor information
 */
class Vendor implements VendorInterface
{
	/**
	 * @var string
	 */
	protected $name = null;

	/**
	 * Relative path to the package directory from the resource directory
	 * @var string
	 */
	protected $path = null;

	/**
	 * Current build number for this vendors packages
	 * @var string
	 */
	protected $version = null;

	/**
	 * Path to the json file containing all the vendors packages
	 * @var string
	 */
	protected $pkgTreePath = null;

	/**
	 * @param	array	$data
	 * @return	Vendor
	 */
	public function __construct(array $data)
	{
		if (! isset($data['name'])) {
			$err = "vendor must have a name defined";
			throw new InvalidArgumentException($err);
		}
		$this->setVendorName($data['name']);

		if (! isset($data['path'])) {
			$err = "vendor must define a path: path to packages";
			throw new InvalidArgumentException($err);
		}
		$this->setPackagePath($data['path']);

		if (! isset($data['version'])) {
			$err = "vendor must define a version: build version";
			throw new InvalidArgumentException($err);
		}
		$this->setVersion($data['version']);

		if (isset($data['tree-path'])) {
			$this->setPackageTreePath($data['tree-path']);
		}
	}

	/**
	 * @return	string
	 */
	public function getVendorName()
	{
		return $this->name;
	}

	/**
	 * @return	string
	 */
	public function getPackagePath()
	{
		return $this->path;
	}

    /**
     * @return  string
     */
    public function getVersion()
    {
        return $this->version;
    }

	/**
	 * @return	string
	 */
	public function getPackageTreePath()
	{
		return $this->pkgTreePath;
	}

	/**
	 * @return	bool
	 */
	public function isPackageTree()
	{
		return is_string($this->pkgTreePath) && ! empty($this->pkgTreePath);
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setVendorName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'vendor name must be a none empty string';
			throw new InvalidArgumentException($err);
		}
		$this->name = $name;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setPackagePath($path)
	{
		if (! is_string($path) || empty($path)) {
			$err = 'package path must be a none empty string';
			throw new InvalidArgumentException($err);
		}
		$this->path = $path;
	}

    /**
     * @return  string
     */
    protected function setVersion($str)
    {  
        if (! is_scalar($str)) {
            $err = 'version must be a scalar value';
            throw new InvalidArgumentException($err);
        }

        $this->version =(string) $str;
        return $this;
    }

	/**
	 * @param	string	$path
	 * @return	null
	 */
	protected function setPackageTreePath($path)
	{
		if (! is_string($path) || empty($path)) {
			$err = "package tree path must be a non empty string";
			throw new InvalidArgumentException($path);
		}

		$this->pkgTreePath = $path;
	}
}
