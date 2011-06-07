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
namespace Appfuel\Framework\App\View;

/**
 * A build item is generally used as a value object holding the information 
 * needed to build one template into a string and assign that string into 
 * another template
 */
interface BuildItemInterface
{
	/**
	 * The key of the template we want to build into a string
	 * 
	 * @return string
	 */
	public function getSource();

	/**
	 * The key of the template we want to assign the results of sources
	 * build into
	 *
	 * @return	string
	 */
	public function getTarget();
	
	/**
	 * The label used when assigning the build results of source into target
	 * 
	 * @return string
	 */
	public function getAssignLabel();
}
