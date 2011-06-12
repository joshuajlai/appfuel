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
 * The connection interface sould be a value object holding only the 
 * information necessary for establishing a connection. The rules for
 * the dsn string are type=value, for example,
 * adapter=mysqli,username=some-user,password=some-password
 */
interface DsnInterface
{
	
	public function getVendor();
	public function getAdapter();
	public function getPort();
	public function getDbName();
	public function getSocket();
	public function getUserName();
	public function getPassword();
}
