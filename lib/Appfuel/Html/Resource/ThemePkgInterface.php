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

/**
 */
interface ThemePkgInterface extends PkgInterface
{
	/**
	 * @return	bool
	 */
	public function isCssFiles();

	/**
	 * @param	string	$path	
	 * @return	array
	 */
	public function getCssFiles($path = null);

	/**
	 * @return	bool
	 */
	public function isAssetFiles();

	/**
	 * @param	string	$path
	 * @return	array
	 */
	public function getAssetFiles($path = null);

}
