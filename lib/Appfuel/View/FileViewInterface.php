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
namespace Appfuel\View;

/**
 * The file view template uses a FileCompositorInterface to compose a phtml
 * file into string. A 
 */
interface FileViewInterface extends ViewInterface
{
	/**
	 * @return	string
	 */
	static public function getResourceDir();

	/**
	 * @param	string	$dir
	 * @return	null
	 */
	static public function setResourceDir($dir);

	/**
	 * Relative file path to template file
	 * @return	null
	 */
	public function getFile();

	/**
	 * @param	string	$file
	 * @return	ViewTemplate
	 */
	public function setFile($file);

	/**
	 * Used with file templates to change the part of the absolute path 
	 * from the root to the relative. When isBase is true the root path
	 * starts at the end of AF_BASE_PATH.
	 *
	 * @throws	InvalidArgumentException	when path is not a string
	 * @param	string	$path
	 * @return	ViewTemplate
	 */
	public function setRelativeRootPath($path, $isBase = true);
}
