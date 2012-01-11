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
namespace Appfuel\App;

use InvalidArgumentException;

/**
 * The site url has the following form: 
 * scheme://<base|cdn>/<resource-dir>/<vendor-ns>/<version>/<root>/<file-path>
 */
class SiteUrl implements SiteUrlInterface 
{
	/**
	 * Flag used to determine scheme
	 * @var bool
	 */
	protected $isSecure = false;
	
	/**
	 * Url scheme used. When the isSecure flag is true an s is appended.
	 * @var string
	 */
	protected $scheme = 'http';

	/**
	 * Base url of the site
	 * @var string
	 */
	protected $base = null;

	/**
	 * Directory name of resources
	 * @var string
	 */
	protected $resourceDir = '';

	/**
	 * Vendor namespace, used when dealing with html resources like yui etc..
	 * @var string
	 */
	protected $vendor = '';

	/**
	 * @var string
	 */
	protected $version = '';

	/**
	 * Controls the path to the build directory which may change depending on
	 * the enviroment you are
	 * @var string
	 */
	protected $relativeRoot = '';

	/**
	 * @param	string	$base
	 * @param	string	$dir
	 * @param	string|number $version
	 * @param	string	$root
	 * @param	bool	$isSecure
	 * @return	SiteUrl
	 */
	public function __construct($base, 
								$dir, 
								$vendor, 
								$version = null, 
								$root = null,
								$isSecure = null)
	{
		$this->setBase($base)
			 ->setResourceDir($dir)
			 ->setVendor($vendor);

		if (null !== $version) {
			$this->setVersion($version);
		}

		if (null !== $root) {
			$this->setRelativeRoot($root);
		}

		if (true === $isSecure) {
			$this->scheme = 'https';
		}
	}

	/**
	 * @param	string	$path
	 * @param	bool	$isFull	  when false exclude scheme://base
	 * @return	string
	 */
	public function getUrl($path = null, $isFull = true)
	{
		$url   = '';
		$base  = $this->getBase();
		$parts = array();
		if (! empty($base) && $isFull !== false) {
			$scheme = $this->getScheme(); 
			$url = "$scheme://$base";
			$parts[] = $url;
		}

		$dir = $this->getResourceDir();
		if (! empty($dir)) {
			$parts[] = "$dir";
		}

		$vendor = $this->getVendor();
		if (! empty($vendor)) {
			$parts[] = $vendor;
		}

		$version = $this->getVersion();
		if (! empty($version)) {
			$parts[] = $version;
		}

		$root = $this->getRelativeRoot();
		if (! empty($root)) {
			$parts[] = $root;
		}

		if (is_string($path) && ! empty($path)) {
			$parts[] = $path;
		}

		return implode("/", $parts);
	}

	/**
	 * @return	string
	 */
	public function getScheme()
	{
		return $this->scheme;
	}

	/**
	 * @return	string
	 */
	public function getBase()
	{
		return $this->base;
	}

	/**
	 * @return	string
	 */
	public function  getResourceDir()
	{
		return $this->resourceDir;
	}

	/**
	 * @return	string
	 */
	public function getVendor()
	{
		return $this->vendor;
	}

	/**
	 * @return	string
	 */	
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @return	string
	 */
	public function getRelativeRoot()
	{
		return $this->relativeRoot;
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$url
	 * @return	null
	 */
	protected function setBase($url)
	{
		if (! is_string($url)) {
			$err = 'base url must be a string';
			throw new InvalidArgumentException($err);
		}

		$this->base = trim($url);
		return $this;
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$dir
	 * @return	null
	 */
	protected function setResourceDir($dir)
	{
		if (! is_string($dir)) {
			$err = 'base url must be a string';
			throw new InvalidArgumentException($err);
		}

		$this->resourceDir = trim($dir);
		return $this;
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$dir
	 * @return	null
	 */
	protected function setVendor($name)
	{
		if (! is_string($name)) {
			$err = 'base url must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->vendor = trim($name);
		return $this;
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	string|number	$nbr
	 * @return	null
	 */
	protected function setVersion($nbr)
	{
		if (! is_string($nbr) && ! is_numeric($nbr)) {
			$err = 'version must be a number or string';
			throw new InvalidArgumentException($err);
		}

		$this->version = trim((string)$nbr);
		return $this;
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$path
	 * @return	null
	 */
	protected function setRelativeRoot($path)
	{
		if (! is_string($path)) {
			$err = 'relative root path must be a string';
			throw new InvalidArgumentException($err);
		}

		$this->relativeRoot = trim($path);
		return $this;
	}
}
