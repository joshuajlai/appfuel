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
 * Write a resource tree to disk
 */
interface ResourceTreeWriterInterface
{
	/**
	 * @param	string	$path	
	 * @param	bool	$isBasePath
	 * @return	TreeBuilder
	 */
	public function writeTree(array $tree, $path = null, $isBasePath = true);

	/**
	 * @return	string
	 */
	public function getDefaultTreeFile();
}
