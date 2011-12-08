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


use RunTimeException,
	InvalidArgumentException;


/**
 * Appfuels implementation of the DbRequestInterface. See this interface 
 * for details about the interface
 */
class DbRequest implements DbRequestInterface
{
	/**
	 * There are three types of request query, multi-query and prepared
	 * @var string
	 */ 
	protected $type = 'query';

	/**
	 * Used by DbHandler to choose correct db connector
	 * @var string
	 */
	protected $strategy = 'write';

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
	public function __construct($sql = null, $type = null, $strategy = null)
	{
		if (null === $strategy) {
			$strategy = 'write';
		}
		$this->setStrategy($strategy);

		if (null === $type) {
			$type = 'query';
		}
		$this->setType($type);

		if (null !== $sql) {
			$this->setSql($sql);
		}
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param	string	$type 
	 * @return	DbRequest
	 */
	public function setType($type)
	{
		$err = 'Invalid Request: type must be ';
		if (empty($type) || ! is_string($type)) {
			$err .= 'a non empty string';
			throw new InvalidArgumentException($err);
		}

		$valid = array('query', 'multi-query', 'prepared-stmt'); 	
		$type  = strtolower($type);
		if (! in_array($type, $valid, true)) {
			$err = 'one of the following -(query|multi-query|prepared-stmt)';
			throw new InvalidArgumentException($err);
		}
		$this->type = $type;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getStrategy()
	{
		return $this->strategy;
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$strategy
	 * @return	DbRequest
	 */
	public function setStrategy($strategy)
	{
		$err = 'Invalid Request: strategy must be ';
		if (empty($strategy) || ! is_string($strategy)) {
			$err .= 'a non empty string';
			throw new InvalidArgumentException($err);
		}

		$strategy = strtolower($strategy);
		if (! in_array($strategy, array('read','write','read-write'), true)) {
				$err .= 'one of -(read|write|read-write)';
			throw new InvalidArgumentException($err);
		}

		$this->strategy = $strategy;
		return $this;
	}

	/**
	 * @return	RequestQuery
	 */
	public function enableReadOnly()
	{
		return $this->setStrategy('read');
	}

	/**
	 * @return	RequestQuery
	 */
	public function enableWrite()
	{
		return $this->setStrategy('write');
	}

	/**
	 * @return	RequestQuery
	 */
	public function enableReadWrite()
	{
		return $this->setStrategy('read-write');
	}

	/**
	 * @return	string
	 */
	public function getSql()
	{
		return $this->sql;
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$sql
	 * @return	QueryRequest
	 */
	public function setSql($sql)
	{
		if (empty($sql) || ! is_string($sql) || !($sql=trim($sql))) {
			$err = "Failed Request : setSql must use a non empty string";
			throw new InvalidArgumentException($err);
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
	 * @throws	InvalidArgumentException
	 * @param	string	$type
	 * @return	RequestQuery
	 */
	public function setResultType($type)
	{
		$err = 'Invalid Request: type must be ';
		if (empty($type) || ! is_string($type)) {
			throw new InvalidArgumentException("$err a non empty string");
		}

		$type = strtolower($type);
		if (! in_array($type, array('name', 'position', 'name-pos'), true)) {
			throw new InvalidArgumentException(
				"$err one of (name|position|name-pos)" 
			);
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
			throw new InvalidArgumentException($err);
		}

		$this->callback = $callback;
		return $this;
	}
}
