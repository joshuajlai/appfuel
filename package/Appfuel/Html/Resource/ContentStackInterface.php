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

use Iterator,
	Countable;

/**
 * Holds blocks of contents. Each block of content is a string
 */
Interface ContentStackInterface extends Iterator, Countable
{
	/**
	 * @return	array
	 */
	public function getAll();

	/**
	 * @return	array
	 */
	public function getKeys();

	/**
	 * @param	string	$content
	 * @return	bool
	 */
	public function isContent($content);

	/**
	 * Calculate the sha1 of the content to use as its key then store it
	 * 
	 * @throws	InvalidArgumentException
	 * @param	string	$content
	 * @return	ContentStack
	 */
	public function add($content);


	/**
	 * @return	ContentStackInterface
	 */
	public function clear();
}
