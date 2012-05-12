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
namespace Appfuel\Orm\DbSource;

use InvalidArgumentException;

/**
 * The orm map is used to map database
 */
interface DbTableMapInterface
{
	/**
	 * @return	string
	 */
	public function getTableName();

	/**
	 * @return	array
	 */
	public function getColumnMap();

	/**
	 * @return	return	string
	 */
	public function getTableAlias();

	/**
	 * @param	string	$member
	 * @return	string | false when not found
	 */
	public function mapColumn($member);

	/**
	 * @param	string	$member
	 * @return	string | false when not found
	 */
	public function mapMember($column);

	/**
	 * @return	array
	 */
	public function getAllColumns();

	/**
	 * @return	array
	 */
	public function getAllMembers();
}
