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
namespace Appfuel\Framework\Db\Sql;

/**
 * Functionality for sql reserved words. This class holds a list of reserved 
 * and determine if a word is in that list. 
 */
interface SqlReservedWordsInterface
{
	/**
	 * return a list of all the key words
	 * 
	 * @return	array
	 */
	public function getWords();
	

	/**
	 * Determine if the the word given is a reserved word. This is suppose
	 * to be case insensitive
	 * 
	 * @param	string	$word
	 * @return	bool
	 */
	public function isReserved($word);
}
