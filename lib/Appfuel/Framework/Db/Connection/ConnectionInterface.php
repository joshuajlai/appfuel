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
namespace Appfuel\Framework\Db\Connection;


/**
 * Connection interface describes the functionality of appfuel connection 
 * class. These classes are vendor specific usually wrapping low level 
 * libraries like myqli. Because they are used by the appfuel db handler which 
 * has no idea about the details of db vendors or their adapters the connection
 * classes must adhere to this interface
 */
interface ConnectionInterface
{
	/**
	 * @return	ConnectionDetailInterface
	 */
	public function getConnectionDetail();
	public function connect();
	public function isConnected();
	public function close();
	public function getDriver();
	public function isDriver();
	public function createStmtDriver();
	public function createAdapter($requestType);
	public function isError();
	public function getErrorCode();
	public function getErrorText();
}
