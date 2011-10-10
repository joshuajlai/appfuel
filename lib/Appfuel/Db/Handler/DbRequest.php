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
namespace Appfuel\Db\Handler;


use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Handler\DbRequestInterface;


/**
 * Handles configuration options for the DbHandler to control which
 * connector to use, type of request, callbacks, return datastructure,
 * setting the sql etc..
 */
class DbRequest implements DbRequestInterface
{
	/**
	 * Type of request made query|multi-query|prepared-stmt
	 * @var string
	 */ 
	protected $requestType = null;

	/**
	 * Valid request types  used by appfuel. 
	 * query		 - default way of sending a query
	 * multi-query	 - send more than one sql query in the request
	 * prepared-stmt - sql is a perpared statement 
	 * @var array
	 */
	protected $validRequestTypes = array('query','multi-query','prepared-stmt');

	/**
	 * Mode the database server is to be treated. The DbHandler will use this
	 * to get the correct connector. 
	 *
	 * @var string
	 */
	protected $serverMode = null;

	/**
	 * Valid server modes used by appfuel. 
	 * read   - read only (slave connections)
	 * write  - read/write (master connections)
	 * ignore - replication not available
	 * @var array
	 */
	protected $validServerModes = array('read', 'write', 'ignore');

	/**
	 * Sql string used to run the query
	 * @var string
	 */
	protected $sql = null;

	/**
	 * Controls the returning db dataset array
	 * @var string
	 */ 
	protected $resultType = 'name';

	/**
	 * Determines the format of the raw dataset. There are three formats
	 *	name		=> dataset returned as associative array with column names,
	 *	position	=> dataset returned as array index by column position
	 *	both		=> dataset returned as both name and position
	 * @var array
	 */
	protected $validResultTypes = array('name', 'position', 'both');

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
     * Used for multiple sql queries it holds the options that map the
	 * the resultset for each sql query. Options include:
     *  resultKey - replaces the number index with resultKey
     *  callback  - filters each row of the result with this callback
     * @var array
     */
    protected $resultOptions = array();

    /**
     * Used for prepared sql it hold the values used in place of the ?
     * @var array
     */
    protected $values = array();

	/**
	 * @param	string	$type	request type (query|multi-query|prepared-stmt)
	 * @param	string	$mode	server mode  (read|write|both|null)
	 * @param	string	$sql	sql to perform
	 * @return	DbRequest
	 */
	public function __construct($type = null, $mode = null, $sql = null)
	{
		if (empty($type)) {
			$type = 'query';
		}
		
		if (empty($mode)) {
			$mode = 'ignore';
		}

		$this->setRequestType($type);
		$this->setServerMode($mode);
		if (null !== $sql) {
			$this->setSql($sql);
		}
	}

	/**
	 * @return	string
	 */
	public function getServerMode()
	{
		return $this->serverMode;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string	$type
	 * @return	RequestQuery
	 */
	public function setServerMode($mode)
	{
		$err = 'Invalid Server Mode: type must be ';
		if (! is_string($mode)) {
			throw new Exception("$err a non empty string");
		}

		$mode = strtolower($mode);
		if (! in_array($mode, $this->validServerModes, true)) {
			$err .= 'one of (' . implode('|', $this->validServerModes) . ')';
			throw new Exception($err);
		}

		$this->serverMode = $mode;
		return $this;
	}

	/**
	 * @return	RequestQuery
	 */
	public function enableReadOnly()
	{
		return $this->setServerMode('read');
	}

	/**
	 * @return	RequestQuery
	 */
	public function enableWrite()
	{
		return $this->setServerMode('write');
	}

	/**
	 * If you have no replication and only use one db server than use this
	 * 
	 * @return	DbRequest
	 */
	public function ignoreServerMode()
	{
		return $this->setServerMode('ignore');
	}

	/**
	 * @return	string
	 */
	public function getRequestType()
	{
		return $this->requestType;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string	$type
	 * @return	DbRequest
	 */
	public function setRequestType($type)
	{
		$err = 'Invalid Request: type must be ';
		if (! $this->isValidString($type)) {
			throw new Exception("$err a non empty string");
		}

		$type = strtolower($type);
		if (! in_array($type, $this->validRequestTypes, true)) {
			$err .= "one of (" . implode('|', $this->validRequestTypes) . ")";
			throw new Exception($err);
		}

		$this->requestType = $type;
		return $this;
	}

	/**
	 * @return	DbRequest
	 */
	public function enableMultiQuery()
	{
		return $this->setRequestType('multi-query');
	}

	/**
	 * @return	DbRequest
	 */
	public function enableQuery()
	{
		return $this->setRequestType('query');
	}

	/**
	 * @return	DbRequest
	 */
	public function enablePreparedStmt()
	{
		return $this->setRequestType('prepared-stmt');
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
     * @return  bool
     */
    public function isSql()
    {  
        return $this->isValidString($this->sql);
    }

    /**
     * Add a sql string to the existsing sql strings
     * 
     * @param   string  $sql
     * @return  MultiQueryRequest
     */
    public function addSql($sql)
    {
        if (! $this->isValidString($sql)) {
            throw new Exception("Invalid sql: must be non empty string");
        }

        if ($this->isSql()) {
			$sql = "{$this->getSql()};$sql";
		}

        $this->setSql($sql);
        return $this;
    }

    /**
     * @param   array   $List
     * @return  DbRequest
     */
    public function loadSql(array $list)
    {  
        foreach ($list as $sql) {
            $this->addSql($sql);
        }

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
		if (! in_array($type, $this->validResultTypes, true)) {
			$err .= 'one of (' . implode('|', $this->validResultTypes) . ') ';
			throw new Exception($err);
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
     * @return  array
     */
    public function getResultOptions()
    {
        return $this->resultOptions;
    }

    /**
	 * Used in multi query request to control the callbacks for the resultset
	 * of each sql query
	 *
     * @param   array  $type
     * @return  DbRequest
     */
    public function setResultOptions(array $options)
    {
        $this->resultOptions = $options;
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

	/**
	 * @param	mixed	$str
	 * @return	bool
	 */
	protected function isValidString($str)
	{
		return is_string($str) && ! empty($str);
	}
	
}
