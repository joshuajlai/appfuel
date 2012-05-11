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

use InvalidArgumentException;

/**
 * A view is a package that has a markup file. The markup file
 * contains the html structure of the view.
 */
interface ViewPkgInterface extends PkgInterface
{
	/**
	 * @return	string
	 */
	public function getMarkupFile($path = null);

	/**
	 * @return	string
	 */
	public function isJsView();
}
