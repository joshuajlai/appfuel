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
namespace Appfuel\Framework\Db\Request;


/**
 * Functionality needed to handle database request for queries
 */
interface RequestInterface
{
	public function getType();
	public function setType($type);
	public function enableReadOnly();
	public function enableWrite();
	public function enableReadWrite();
	public function getSql();
	public function setSql($sql);
	public function getResultType();
	public function setResultType($type);
	public function enableResultBuffer();
	public function disableResultBuffer();
	public function isResultBuffer();
	public function getCallback();
	public function setCallback($callback);
}
