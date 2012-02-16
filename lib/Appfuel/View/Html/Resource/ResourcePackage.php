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
namespace Appfuel\View\Html;

use InvalidArgumentException;

/**
 */
class ResourcePackage implements ResourcePackageInterface
{
	/**
	 * @var ManifestInterface
	 */
	protected $manifest = null;

	/**
	 * @param	string|ViewInterface $view	
	 * @param	string				 $htmlDocFile	path to phtml file
	 * @param	HtmlTagFactory		 $factory
	 * @return	HtmlPage
	 */
	public function __construct(ManifestInterface $manifest)
	{
		$this->manifest = $manifest;
	}

	/**
	 * @return	ManifestInterface
	 */
	public function getManifest()
	{
		return $this->manifest;
	}

}
