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
 */
interface MysqlSelectKeywordsInterface
{
	/**
	 * @return	string
	 */
	public function getSeparator();

	/**
	 * @param	string	$sep
	 * @return	null
	 */
	public function setSeparator($sep);

	/**
	 * @return	array
	 */
	public function getAllKeywords();

	/**
	 * @return	array
	 */
	public function getEnabledKeywords();

	/**
	 * @param	array	$list
	 * @return	null
	 */
	public function enableKeywords(array $list);

	/**
	 * @param	string	$word
	 * @return	null
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
