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
namespace Appfuel\Framework\Db\Mysql\DataType;


/**
 * Decribes the functionality of Integer datatypes
 */
interface NumberTypeInterface
{
	public function getSqlUnsigned();
	public function getSqlZeroFill();
	public function getSqlAutoIncrement();

	public function enableUnsigned();
	public function enableSigned();
	public function isUnsigned();
	public function getDisplayWidth();
	public function setDisplayWidth($width);
	public function enableZeroFill();
	public function disableZeroFill();
	public function isZeroFill();
	public function isAutoIncrement();
	public function enableAutoIncrement();
	public function disableAutoIncrement();
}
