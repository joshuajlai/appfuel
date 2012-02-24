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
namespace Appfuel\DataSource\Db;

use InvalidArgumentException;

/**
 * Value object used to hold connection details to a database server. The 
 * array keys are fixed as:
 *
 * name: name of the database to connect to
 * host: database server host or ip
 * user: name of the user connecting
 * pass: password of the user connecting
 * opt:  array of options specific to the database vendor
 * 
 */
interface DbConnDetailInterface
{
	/**
	 * @return string
	 */
	public function getHost();

	/**
	 * @return string
	 */
	public function getUserName();

	/**
	 * @return string
	 */
	public function getPassword();

	/**
	 * @return string
	 */
	public function getDbName();

	/**
	 * @return string
	 */
	public function getOptions();
}
