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
namespace Appfuel\Db\Schema;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Schema\PrimaryKeyInterface;

/**
 * Vendor agnostic object that decribes a table column. The column is designed
 * to be as dumb as possible, holding only critical info. This means the column
 * does not even know if it is a key of an index, that job is delagated to the
 * table.
 */
class PrimaryKey implements PrimaryKeyInterface
{
	/**
	 * @var array
	 */
	protected $columns = array();

	/**
	 * @param	array|DictionaryInterface	$data  list of column attrs
	 * @return	Column
	 */
	public function __construct($data)
	{
		if (empty($data)) {
			throw new Exception("columns for primary key can not be empty");
		}

		if (is_string($data)) {
			$columns = array($data);
		} 
		else if (is_array($data)) {
			foreach ($data as $index => $col) {
				if (empty($col) || ! is_string($col)) {
					throw new Exception("invalid columns at -($index)");
				}
			}
			$columns = $data;
		}
		else {
			throw new Exception("column(s) must be a string or array");

		}
	
		$this->columns = $columns;
	}

	/**
	 * @return	string
	 */
	public function getColumnNames()
	{
		return $this->columns;
	}

	/**
	 * @return	bool
	 */
	public function isKey($name)
	{
		if (empty($name) || 
			! is_string($name) ||
			!  in_array($name, $this->columns)) {
			return false;
		}

		return true;
	}
}
