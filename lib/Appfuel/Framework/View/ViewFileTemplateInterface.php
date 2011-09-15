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
namespace Appfuel\Framework\View;

use Appfuel\Framework\File\PathFinderInterface;

/**
 * This is a view template with path support for the template formatter.
 * The pathfinder allows the template to not care of how to resolve the
 * absolute path to find the template file needed for the template formatter.
 */
interface ViewFileTemplateInterface extends ViewTemplateInterface
{
	/**
	 * @return	PathFinderInterface
	 */
	public function getPathFinder();
	
	/**
	 * @param	PathFinderInterface	$pathFinder
	 * @return	ViewFileTemplateInterface
	 */
	public function setPathFinder(PathFinderInterface $pathFinder);
}
