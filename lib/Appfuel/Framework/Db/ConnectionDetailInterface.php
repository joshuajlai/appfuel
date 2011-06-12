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
namespace Appfuel\Framework\Db;


/**
 * The connection detail  is used to hold all information necessary for 
 * establishing a connection. The rules for parsing and parsing the string 
 * that contains a dsn or other connection description has nothing to do with 
 * the connnection detail it is handler by the connection string parser
 */
interface ConnectionDetailInterface
{
	
	public function getVendor();
	public function getAdapter();
	public function getPort();
	public function getDbName();
	public function getSocket();
	public function getUserName();
	public function getPassword();
}
