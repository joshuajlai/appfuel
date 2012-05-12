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
namespace Appfuel\Html\Resource\Yui;

use InvalidArgumentException,
	Appfuel\Html\Resource\FileStackInterface;

/**
 * Adds sorting based on yui3 after property
 */
interface Yui3FileStackInterface extends FileStackInterface
{
	/**
	 * @param	string	$type
	 * @param	string	$file
	 * @param	string	$afterFile
	 * @return	FileStack
	 */
	public function addAfter($type, $file, $afterFile);

	/**
	 * @param	string	$type
	 * @return	array
	 */
	public function getAfter($type);

	/**
	 * @return	Yui3FileStack
	 */
	public function sortByPriority();

	/**
	 * @param	string	$type	
	 * @return	Yui3FileStack
	 */
	public function resolveAfter($type);
}
