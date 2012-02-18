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
 * Value object used to describe the vendor information and package list
 */
class ResourceVendor implements ResourceVendorInterface
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
	 * Relative path to the package directory from the resource directory
	 * @var string
	 */
	protected $pkgPath = null;

	/**
	 * Relative path to the build directory
	 * @var string
	 */
	protected $buildPath = null;

	/**
	 * Current build number for this vendors packages
	 * @var string
	 */
	protected $version = null;

	/**
	 * @var PackageFileListInterface
	 */
	protected $packages = null; 
	
	/**
	 * @param	string|ViewInterface $view	
	 * @param	string				 $htmlDocFile	path to phtml file
	 * @param	HtmlTagFactory		 $factory
	 * @return	HtmlPage
	 */
	public function __construct(array $data)
	{
		if (isset($data['name'])) {
			$this->setVendorName($data['name']);
		}

		if (isset($data['desc'])) {
			$this->setVendorDescription($data['desc']);
		}
		
		if (isset($data['pkg-path'])) {
			$this->setPackagePath($data['pkg-path']);
		}

		if (isset($data['build-path'])) {
			$this->setBuildPath($data['build-path']);
		}

		if (! isset($data['version'])) {
			$this->setVersion($data['version']);
		}

		if (isset($data['pkg-list'])) {
			$this->setPackageList($data['pkg-list']);
		}

		if (isset($data['depends'])) {
			$this->setDependencies($data['depends']);
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
	public function getVendorDescription()
	{
		return $this->desc;
	}
	
	/**
	 * @return	string
	 */
	public function getPackagePath()
	{
		return $this->pkgPath;
	}

	/**
	 * @return	string
	 */
	public function getBuildPath()
	{
		return $this->buildPath;
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
	public function getAllPackageNames()
	{
		return array_keys($this->packages);
	}

	public function getAllPackageDirs()
	{
		return array_values($this->packages);
	}

	/**
	 * @return array
	 */
	public function getPackageList()
	{
		return $this->packages;
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
	protected function setVendorDescription($desc)
	{
		if (! is_string($desc)) {
			$err = 'vendor description must be a string';
			throw new InvalidArgumentException($err);
		}

		$this->desc = $desc;
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
		$this->pkgPath = $path;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setBuildPath($path)
	{
		if (! is_string($path) || empty($path)) {
			$err = 'build path must be a none empty string';
			throw new InvalidArgumentException($err);
		}
		$this->buildPath = $path;
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
	 * @param	array	$list
	 * @return	null
	 */
	protected function setPackages(array $list)
	{
		if ($list === array_values($list)) {
			$err = 'package list must be an assoc array of name=>path/to/dir';
			throw new InvalidArgumentException($err);
		}

		foreach ($list as $name => $path) {
			if (! is_string($name) || empty($name)) {
				$err = 'package name must be a non empty string';
				throw new InvalidArgumentException($err);
			}

			if (! is_string($path) || empty($path)) {
				$err = 'package relative dir path must be a non empty string';
				throw new InvalidArgumentException($err);
			}
		}

		$this->packages = $list;
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
