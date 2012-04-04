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
interface AppViewInterface extends AppfuelManifestInterface
{
	/**
	 * @return	string
	 */
	public function getHtmlPage();

	/**
	 * @return	string
	 */
	public function getMarkupFile();

	/**
	 * @return	string
	 */
	public function getJsInitFile();

	/**
	 * @return	bool
	 */
	public function isJsInitFile();

	/**
	 * @return	string
	 */
	public function getLayers();
}
