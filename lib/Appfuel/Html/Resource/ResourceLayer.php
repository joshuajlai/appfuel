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
 */
class ResourceLayer 
{
	/**
	 * @var string
	 */
	protected $name = null;

	/**
	 * Name of the file used when layer is rolled up. 
	 * @var string
	 */
	protected $filename = null;

	/**
	 * List of packages and vendor info keyed by vendor and package name
	 * @var array
	 */
	protected $data = array();

	/**
	 * @param	array $data	
	 * @return	PackageManifest
	 */
	public function __construct($name, $filename = null)
	{
		$this->setLayerName($name);
		if (null === $filename) {
			$filename = $name;
		}

		$this->setFilename($filename);
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
	 * @return	string
	 */
	public function getFilename()
	{
		return $this->filename;
	}

	/**
	 * @param	string	$name
	 * @return	ResourceLayer
	 */
	public function setFilename($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'filename must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->filename = $name;
		return $this;
	}

	/**
	 * @param	VendorInterface $vendor
	 * @param	PackageInterface $package
	 * @return	ResourceLayer
	 */
	public function addPackage(VendorInterface $vendor, PackageInterface $pkg)
	{
		
	}

}
