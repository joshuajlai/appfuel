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
namespace Appfuel\DataSource\Db\Mysql\Sql\Select;

/**
 * This interface is used to turn a list of case insensitive mysql keywords,
 * used in a mysql select statement, into a formatted string. It handles rules
 * for the keywords and allows for basic formatting through a keyword separator
 * character.
 */
interface MysqlSelectKeywordsInterface
{
	/**
	 * @return	string
	 */
	public function getSeparator();

	/**
	 * 1) must be non empty string. 
	 *    throw InvalidArguementException otherwise
	 * 
	 * @param	string	$sep
	 * @return	null
	 */
	public function setSeparator($sep);

	/**
	 * 1) return all keywords defined, both enabled and disabled
	 *
	 * @return	array
	 */
	public function getAllKeywords();

	/**
	 * 1) return only those keywords that have been enabled
	 *
	 * @return	array
	 */
	public function getEnabledKeywords();

	/**
	 * 1) for every item in the list apply 'enableKeyword' 
	 *
	 * @param	array	$list
	 * @return	null
	 */
	public function enableKeywords(array $list);

	/**
	 * 1) when $word is not a string throw an InvalidArgumentException
	 * 2) turn $word to all uppercase
	 * 3) if $word is a defined keyword then enable it
	 * 4) ensure any rules that exist for the keyword are applied 
	 *	 
	 * Rules:
	 *	1) ALL | DISTINCT | DISTINCTROW	 only one of these can be enabled and
	 *	   the others must be disabled
	 *  2) SQL_SMALL_RESULT | SQL_BIG_RESULT when one is enabled the other is 
	 *     disabled
	 *  3) SQL_CACHE | SQL_NO CACHE when one is enabled the other is disabled
	 *
	 * @param	string	$word
	 * @return	bool
	 */
	public function enableKeyword($word);

	/**
	 * @return	null
	 */
	public function reset();

	/**
	 * @param	string	$sep
	 * @return	string
	 */
	public function build();

	/**
	 * @return string
	 */
	public function __toString();
}
