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
namespace Appfuel\Db\Mysql\Mysqli;

use Appfuel\Db\DbError;

/**
 * Mysqli adapter exposes the mysqli functionality though the
 * the adapter interface
 */
class MultiQueryError extends DbError
{
	/**
	 * Number or key used to indicate which sql stmt this error has occured 
	 * @var	mixed	int | string
	 */
	protected $index = null;

	/**
	 * Override error constructor to add the key (index number or label) of
	 * which data set (sql stmt) this error occured for
	 *
	 * @param	mixed	int | string	$code
	 * @param	string	$code
	 * @param	string	$msg
	 * @param	string	$sqlState
	 * @return	MultiQueryError
	 */
	public function __construct($key, $code, $msg = null, $sqlState = null)
	{
		parent::__construct($code, $msg, $sqlState);
		$this->index = $key;
	}

	/**
	 * @return	mixed	int | string
	 */
	public function getIndex()
	{
		return $this->index;
	}
}
