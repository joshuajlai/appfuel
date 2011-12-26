<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\View;

use SplFileInfo,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\PathFinderInterface;

/**
 */
class UiPathFinder implements PathFinderInterface
{
	/**
	 * Directory where all user interface resources are kept. The location of
	 * this directory is always relative to the AF_BASE_PATH
	 * @var string
	 */
	protected $resourceDir = 'ui';

	/**
	 * User Interface as seperatated by vender so multiple vendors can easily
	 * be shared in a single app. 
	 * @var string
	 */
	protected $vendorDir = 'appfuel';
	
    /**
     * Override the resource or vendor directory if given. We don't use the
	 * setters (setResourceDir/setVendorDir) because they update the relative
	 * root path, for which we will do (no need to do it twice
	 * 
     * @param   string  $path
     * @param   bool    $isBasePath
     * @return  File
     */
    public function __construct($resource = null, $vendor = null)
    {
		if (null !== $resource) {
			if (! is_string($vendor)) {
				$err = 'resource dir must be a string';
				throw new InvalidArgumentException($err);
			}
			$this->resourceDir = $resource;
		}

		if(null !== $vendor) {
			if (! is_string($vendor)) {
				$err = 'vendor dir must be a string';
				throw new InvalidArgumentException($err);
			}
			$this->vendorDir = $vendor;
		}

		$isBasePath = true;
		$relativePath = "{$this->resourceDir}/{$this->vendorDir}";
		parent::__construct($relativePath, $isBasePath);
    }

	/**
	 * @param	string
	 */
	public function getResourceDir()
	{
		return $this->resourceDir;
	}

	/**
	 * Assign the resource directory and update the relative root path with 
	 * the new resource directory
	 *
	 * @param	string $dir
	 * @return	UiPathFinder
	 */
	public function setResourceDir($dir)
	{
		if (! is_string($dir)) {
			throw new InvalidArgumentException("resource dir must be a string");
		}

		$this->resourceDir = $dir;
		$path = "$dir/{$this->getVendorDir()}";
		return $this->setRelativeRootPath($path);
	}

	/**
	 * @param	string
	 */
	public function getVendorDir()
	{
		return $this->vendor;
	}

	/**
	 * Update the relative root path to reflect the new vendor directory
	 * 
	 * @param	string $dir
	 * @return	UiPathFinder
	 */
	public function setVendorDir($dir)
	{
		if (! is_string($dir)) {
			throw new InvalidArgumentException("vendor dir must be a string");
		}

		$this->vendorDir = $dir;
		$path = "{$this->getResourceDir()}/$dir";
		return $this->setRelativeRootPath($path);
		return $this;
	}


}
