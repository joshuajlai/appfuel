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
namespace Appfuel\View;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Html\Resource\PkgName;

/**
 * The file view template uses a FileCompositorInterface to compose a phtml
 * file into string. A 
 */
interface TemplateInterface extends ViewInterface
{
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
	 * @return	PkgNameInterface
	 */
	public function getViewPkgName()

	/**
	 * @param	PkgNameInterface $name
	 * @return	FileTemplate
	 */
	public function setViewPkgName(PkgNameInterface $name)

	/**
	 * @param	string	$name
	 * @param	string	$defaultVendor
	 * @return	PkgName
	 */
	public function createViewPkgName($name, $defaultVendor = null)

	/**
	 * @return	bool
	 */
	public function isViewPackage()

	/**
	 * @param	string	$name 
	 * @param	string	$defaultVendor
	 * @return	FileTemplate
	 */
	public function setViewPackage($name, $defaultVendor = null);
}
