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
class Package implements PackageInterface
{
	/**
	 * @var	ManifestInterface
	 */
	protected $manifest = null;
	
	/**
	 * @var string
	 */
	protected $srcPath = null;

	/**
	 * @param	AppfuelManifestInterface $manifest
	 * @return	AppfuelPackage
	 */
	public function __construct(AppfuelManifestInterface $manifest)
	{
		$this->manifest = $manifest;
	}

	/**
	 * @return	string
	 */
	public function getManifest()
	{
		return $this->name;
	}

	/**
	 * @return	string
	 */
	public function getSourcePath()
	{
		return $this->getManifest()
					->getSourcePath();
	}

	/**
	 * @return	string
	 */
	public function getFileName()
	{
		return $this->getManifest()
					->getFileName();
	}

	/**
	 * @param	string
	 */
	public function getPackageFilePath()
	{

	}

	public function getAllFilePaths()
	{

	}
}
