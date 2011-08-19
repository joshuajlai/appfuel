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
 */
interface CallbackErrorInterface extends DbErrorInterface
{
	public function setCallbackType($name);
	public function getCallbackType();
	public function getRowNumber();
	public function setRowNumber($index);
	public function getRow();
	public function setRow($data = null);
}
