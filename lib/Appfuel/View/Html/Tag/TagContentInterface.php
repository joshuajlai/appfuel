<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\View\Html\Tag;

/**
 */
interface TagContentInterface
{
	/**
	 * @return string
	 */
	public function getSeparator();

	/**
	 * Used during content building to separate each block of content 
	 *
	 * Requirements:
	 * 1) it is a InvalidArgumentException for $char to non scalar
	 * 2) return TagContentInterface
	 *
	 * @throws	InvalidArgumentException
	 * @param	scalar	$char
	 * @return	Tag
	 */
	public function setSeparator($char);

	/**
	 * Add content a content block to the content list. Each block will 
	 * be concatenated togather with the content sepearator during build.
	 *
	 * Requirements:
	 * 1) $data must be a scalar value of an object that implements __toString
	 *    you must throw an InvalidArgumentException otherwise. This helps
	 *	  keep TagContentInterface::build simple
	 * 2) $action must be a non empty string. 
	 *    you must throw an InvalidArgumentException otherwise
	 * 3) you must normalize $action to be all lower case
	 * 4) if action is not (append|prepend|replace) 
	 *	  you must throw an InvalidArgumentException
	 *    append  - must add the content block to the end of the array
	 *    prepend - must add the content block to the beginning of the array
	 *    replace - must replace the content with an array of one which is 
	 *				the content block given
	 *	 
	 * 5) regardless of the action you must convert the content block to a 
	 *    a string during the assignment
	 * 6) return TagContentInterface
	 * 
	 * @param	mixed scalar|object	$data	
	 * @param	string				$action  (append|prepend|replace)	
	 * @return	TagContentInterface
	 */
    public function add($data, $action = 'append');

	/**
	 * Return a content block at a given index.
	 *
	 * Requirements:
	 * 1) when index is null return all the content
	 * 2) when index is not an integer or out of bounds return false
	 *    otherwise return the content block at that index
	 *
	 * @param	int	 $index	default null
	 * @return	string|false
	 */
	public function get($index = null);

	/**
	 * Requirements:
	 * 1) return the number of content blocks added
	 *
	 * @return int
	 */
	public function count();

	/**
	 * Requirements:
	 * 1) must return true when content count is 0 and false otherwise
	 *
	 * @return	bool
	 */
	public function isEmpty();

	/**
	 * Requirements:
	 * 1) when index is null replace the content with an empty array
	 * 2) when index is an int and in range unset that content block
	 * 3) when index is not an integer or out of range return false
	 *
	 * @param	int	$index	default	null
	 * @return	null | false when invalid index
	 */
	public function clear($index = null);

	/**
	 * Build the list of content blocks into a string
	 *
	 * Requirements:
	 * 1) concatenate each content block with the character specified by
	 *    TagContentInterface::getSeparator
	 * 2) when no content exists return an empty string
	 * 3) ensure any tailing separator characters has been trimed of the
	 *    left and right side of the result
	 * 4) ensure no exceptions are throw, must be used from __toString
	 *
	 * @return string
	 */
	public function build();

	/**
	 * Requirements:
	 * 1) must call build
	 *
	 * @return string
	 */
	public function __toString();
}
