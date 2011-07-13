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
namespace Appfuel\Db\Request;


use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Request\PreparedRequestInterface;


/**
 * Functionality needed to handle database request for queries
 */
class PreparedRequest 
	extends QueryRequest 
		implements PreparedRequestInterface
{
	/**
	 * Hold the values used in the prepared sql
	 * @var array
	 */
	protected $values = array();
	
	/**
	 * @return	string
	 */
	public function getValues()
	{
		return $this->values;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string	$type
	 * @return	RequestQuery
	 */
	public function setValues(array $values)
	{
		$this->values = $values;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isValues()
	{
		return count($this->values) > 0;
	}
}
