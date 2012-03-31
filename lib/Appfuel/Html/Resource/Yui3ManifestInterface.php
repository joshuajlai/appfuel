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

/**
 */
interface Yui3ManifestInterface
{
	/**
	 * @return	string
	 */
	public function getPackageName();

	/**
	 * @return	array
	 */
	public function getRequire();

	/**
	 * @return	bool
	 */
	public function isRequire();

	/**
	 * @return	array
	 */
	public function getUse();

	/**
	 * @return	bool
	 */
	public function isUse();

	/**
	 * @return array
	 */
	public function getAfter();

	/**
	 * @return	bool
	 */
	public function isAfter();

	/**
	 * @return	array
	 */
	public function getLang();

	/**
	 * @return	bool
	 */
	public function isLang();

	/**
	 * @return	bool
	 */
	public function isSkinnable();

	/**
	 * @return	bool
	 */
	public function isCss();
}
