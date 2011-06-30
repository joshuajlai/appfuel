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
namespace Appfuel\Framework\Db\Adapter;


/**
 * Desribed the functionality needed by an object representing the reponse 
 * from a database.
 */
interface DbResponseInterface
{
	public function getData();
	public function isError();
	public function isSuccess();
	public function getError();
	public function getStatus();
}
