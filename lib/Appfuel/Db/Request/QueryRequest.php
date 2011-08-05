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
	Appfuel\Framework\Db\Request\RequestInterface;


/**
 * Functionality needed to handle database request for queries
 */
class QueryRequest implements RequestInterface
{
	/**
	 * Code is used to determine which database adapter is needed to service
	 * this request
	 * @var string
	 */ 
	protected $code = 'query';

	/**
	 * Used to determine the type of request for replication systems. 
	 * Allowed values are read | write | both
	 * @var string
	 */
	protected $type = 'read';

	/**
	 * Sql string used to run the query
	 * @var string
	 */
	protected $sql = null;

	/**
	 * Determines the format of the raw dataset. There are three formats
	 *	name		=> dataset returned as associative array with column names,
	 *	position	=> dataset returned as array index by column position
	 *	both		=> dataset returned as both name and position
	 * @var string
	 */ 
	protected $resultType = 'name';

	/**
	 * Flag used to tell the db adapter to buffer the results or with large
	 * datasets not to buffer the results
	 * @var bool
	 */
	protected $isResultBuffer = true;

	/**
	 * A callback can be given that adapter will use to pass a raw row
	 * for every row in the dataset
	 * @var mixed
	 */
	protected $callback = null;

	/**
	 * @param	string	$type	type of db operation is it read | write | both
	 * @param	string	$sql	optionally set the sql string
	 * @return	QueryRequest
	 */
	public function __construct($type, $sql = null)
	{
		$this->setType($type);
		if (null !== $sql) {
			$this->setSql($sql);
		}
	}

	/**
	 * @return string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @return	string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string	$type
	 * @return	RequestQuery
	 */
	public function setType($type)
	{
		$err = 'Invalid Request: type must be ';
		if (! is_string($type) || empty($type)) {
			throw new Exception("$err a non empty string");
		}

		$type = strtolower($type);
		if (! in_array($type, array('read', 'write', 'both'), true)) {
			throw new Exception("$err one of (read|write|both)" );
		}

		$this->type = $type;
		return $this;
	}

	/**
	 * @return	RequestQuery
	 */
	public function enableReadOnly()
	{
		return $this->setType('read');
	}

	/**
	 * @return	RequestQuery
	 */
	public function enableWrite()
	{
		return $this->setType('write');
	}

	/**
	 * @return	RequestQuery
	 */
	public function enableReadWrite()
	{
		return $this->setType('both');
	}

	/**
	 * @return	string
	 */
	public function getSql()
	{
		return $this->sql;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string	$sql
	 * @return	QueryRequest
	 */
	public function setSql($sql)
	{
		if (! $this->isValidString($sql)) {
			$err = "Failed Request : setSql must use a non empty string";
			throw new Exception($err);
		}

		$this->sql = $sql;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getResultType()
	{
		return $this->resultType;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string	$type
	 * @return	RequestQuery
	 */
	public function setResultType($type)
	{
		$err = 'Invalid Request: type must be ';
		if (! $this->isValidString($type)) {
			throw new Exception("$err a non empty string");
		}

		$type = strtolower($type);
		if (! in_array($type, array('name', 'position', 'both'), true)) {
			throw new Exception("$err one of (name|position|both)" );
		}

		$this->resultType = $type;
		return $this;
	}

	/**
	 * @return	RequestQuery
	 */
	public function enableResultBuffer()
	{
		$this->isResultBuffer = true;
		return $this;	
	}

	/**
	 * Use this when you expect back a very large dataset
	 * @return	RequestQuery
	 */
	public function disableResultBuffer()
	{
		$this->isResultBuffer = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isResultBuffer()
	{
		return $this->isResultBuffer;
	}

	/**
	 * @return	mixed
	 */
	public function getCallback()
	{
		return $this->callback;
	}
	
	/**
	 * @return	mixed
	 */
	public function setCallback($callback)
	{
		if (! is_callable($callback)) {
			$err = "Request failed: setCallback param must be callable";
			throw new Exception($err);
		}

		$this->callback = $callback;
		return $this;
	}

	/**
	 * @param	mixed	$str
	 * @return	bool
	 */
	protected function isValidString($str)
	{
		return is_string($str) && ! empty($str);
	}
	
}
