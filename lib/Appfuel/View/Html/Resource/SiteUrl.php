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
 */
class SiteUrl implements SiteUrlInterface 
{
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
	 * base url of the cdn used by the site
	 * @var string
	 */
	protected $cdn = null;

	/**
	 * 
	 * @var string
	 */
	protected $relativeRoot = '';

	/**
	 * @var string
	 */
	protected $version = '';

	/**
	 * Flag used to determine scheme
	 * @var bool
	 */
	protected $isSecure = false;

	/**
	 * @param	string	$base
	 * @param	string	$cdn
	 * @return	SiteUrl
	 */
	public function __construct($base, $cdn = null)
	{
		$this->setBase($base);
		if (null !== $cdn) {
			$this->setCdn($cdn);
		}
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
	public function getCdn()
	{
		return $this->cdn;
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
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$url
	 * @return	null
	 */
	protected function setCdn($url)
	{
		if (! is_string($url) || ! ($url = trim($url))) {
			$err = 'base url must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->cdn = $url;
	}
}
