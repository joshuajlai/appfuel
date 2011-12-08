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
namespace Appfuel\Db;


/**
 * This interface is a vendor agnostic view of the database connection. A
 * vendors adapter will use this interface to connect to the database.
 */
interface ConnectionDetailInterface
{
	public function getAdapter();
	public function getHost();
	public function getUserName();
	public function getPassword();
	public function getDbName();
	public function getPort();
	public function getSocket();
}
