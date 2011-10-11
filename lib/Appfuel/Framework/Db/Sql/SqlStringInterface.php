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
 * Used by any object that needs to produce sql as ouput
 */
interface SqlStringInterface
{
	
	/**
	 * @return	string
	 */	
	public function buildSql();

	/**
	 * @return	SqlStringInterface
	 */
	public function enableUpperCase();

	/**
	 * @return	SqlStringInterface
	 */
	public function disableUpperCase();

	/**
	 * @return	bool
	 */
	public function isUpperCase();

	/**
	 * So the sql can be used in the context of a string
	 * 
	 * @return	string
	 */
	public function __toString();
}
