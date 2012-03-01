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
namespace Appfuel\DataSource\Db;


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
     * Hold the values used in prepared sql
     * @var array
     */
    protected $values = array();

    /**
     * For multi-query requests this holds the options that map to each 
	 * resultset. Options include:
     *  resultKey - replaces the number index with resultKey
     *  callback  - filters each row of the result with this callback
     * @var array
     */
    protected $multiResultOptions = array();

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
		if (empty($type) || ! is_string($type)) {
			$err = 'DbRequest type must be a non empty string';
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
     * Add a sql string by appending it to the current sql string concatenated
	 * with a ';'.
     * 
	 * @throws	InvalidArgumentException
     * @param   string  $sql
     * @return  DbRequest
     */
    public function addSql($sql)
    {
        if (empty($sql) || ! is_string($sql) || ! ($sql = trim($sql))) {
            throw new InvalidArgumentException(
				"Invalid sql: must be non empty string"
			);
        }

        if (! $this->isSql()) {
            $this->setSql($sql);
            return $this;
        }

        $this->setSql("{$this->getSql()};$sql");
        return $this;
    }

    /**
     * @param   array   $sqlList
     * @return  DbRequest
     */
    public function loadSql(array $sqlList)
    {
        foreach ($sqlList as $sqlStr) {
            $this->addSql($sqlStr);
        }

        return $this;
    }

    /**
     * @return  bool
     */
    public function isSql()
    {
        return is_string($this->sql) && ! empty($this->sql);
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
     * @return  string
     */
    public function getMultiResultOptions()
    {  
        return $this->multiResultOptions;
    }

    /**
     * @param   array  $options
     * @return  DbRequest
     */
    public function setMultiResultOptions(array $options)
    { 
        $this->multiResultOptions = $options;
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

    /**
     * @return  array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param   array  $values
     * @return  DbRequest
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
