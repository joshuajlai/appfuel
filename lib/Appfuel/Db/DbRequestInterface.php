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


/**
 * This is a vendor agnostic interface used to decribe actual request on
 * the database server. The vender adapters use this interface to implement
 * the server specific request.
 */
interface DbRequestInterface
{
	/**
	 * @return	string
	 */
	public function getType();
	
	/**
	 * There are three types of requests 
	 * query		 - regular query 
	 * multi-query	 - issues more than one query in the same connection
	 * prepared-stmt - issues a prepared statement instead of a query
	 *
	 * 1) It is an InvalidArgumentException when the type is empty or not a
	 *	  string.
	 * 2) It is an InvalidArgumentException when the type is not one of the 
	 *	  three above
	 * 3) type is always converted to lowercase
	 *
	 * @throws	InvalidArgumentException	
	 * @param	string	$type
	 * @return	DbRequestInterface
	 */
	public function setType($type);

	/**
	 * @return	string
	 */
	public function getStrategy();
	
	/**
	 * Strategy is used by the DbHandler to determine which connector to use.
	 * It was implemented as a way of filtering read and write request to 
	 * different database servers. There are three strategies:
	 * read			- only sql that issues selects 
	 * write		- only sql that issues inserts, updates, delete
	 * read-write	- any write that also has selects in it. 
	 *				  (this is more for reporting as it will still go to the
     *				   same server as write)
	 *
     * 1) It is an InvalidArgumentException when the strategy is empty or not a
     *	  string.
     * 2) It is an InvalidArgumentException when the strategy is not one of the 
     *	  three above
     * 3) strategy is always converted to lowercase
	 * 
	 * @throws	InvalidArgumentException
	 * @param	string	$name
	 * @return	DbRequestInterface
	 */
	public function setStrategy($name);

	/**
	 * Alias to setStrategy('read')
	 * 
	 * @return	DbRequestInterface
	 */
	public function enableReadOnly();

	/**
	 * Alias to setStrategy('write')
	 * 
	 * @return	DbRequestInterface
	 */
	public function enableWrite();

	/**
	 * Alias to setStrategy('read-write')
	 * 
	 * @return	DbRequestInterface
	 */
	public function enableReadWrite();

	/**
	 * @return	string	
	 */
	public function getSql();
	
	/**
	 * The actual sql to be executed on the database server
	 * 
	 * 1) It is an InvalidArgumentException for sql to be a non empty string.
	 * 2) All sql must be trimmed for whitespaces (both left and right of)
	 *
	 * @throws	InvalidArgumentException	
	 * @param	string	$sql
	 * @return	DbRequestInterface
	 */
	public function setSql($sql);

    /**
     * Add a sql string by appending it to the current sql string concatenated
     * with a ';'.
     * 
	 * 1) Must throw InvalidArgumentException when array entry is empty or
	 *	  or is not a string
	 * 2) Each array entry string must be trimmed for whitespaces
	 *
     * @throws  InvalidArgumentException
     * @param   string  $sql
     * @return  DbRequest
     */
    public function addSql($sql);

    /**
	 * Add each array entry to the sql string that was assigned with setSql
	 *
	 * 1) Must throw InvalidArgumentException when array entry is empty or
	 *	  or is not a string
	 * 2) Each array entry string must be trimmed for whitespaces
	 *
	 * All the requirements can be met is you use addSql interface for each
	 * array entry
	 *
	 * @throws	InvalidArgumentException
     * @param   array   $sqlList
     * @return  DbRequest
     */
    public function loadSql(array $sqlList);

    /**
	 * Return true when the sql is a non empty string otherwise return false
	 *
     * @return  bool
     */
    public function isSql();

	/**
	 * @return	string
	 */
	public function getResultType();

	/**
	 * The result type detemines the array format of the dataset from the 
	 * server. There are three types:
	 * 
     *  name        dataset returned as assoc array with column names as keys
     *  position    dataset returned as an array index by column position
     *  name-pos	dataset returned as both name and position so column name
	 *				keys are present and position key are present. Note data
	 *				is duplicated and large then the other two.
	 *
	 * 1) It is an InvalidArgumentException to have type as a non empty string
	 * 2) It is an InvalidArgumentException for type to be anything other than
	 *    one of the three types
	 * 3) All types are converted to lowercase
	 * 
	 * @throws	InvalidArgumentException
	 * @param	string	$type
	 * @return	DbRequestInterface
	 */
	public function setResultType($type);

    /**
     * @return  string
     */
    public function getMultiResultOptions();

    /**
	 * In a multi query the response object returned has multiple datasets 
	 * keyed by the index position of the submitted query, so if you sent
	 * a string with 5 queries then the indexes are 0-4, these can be 
	 * replaced with a string by providing a array of options keyed by the
	 * the numeric key. The adapter looks for the numberic key then looks 
	 * for 'result-key and callback in that array of options.
	 * the 'result-key' is used to change the index of the resultset to 
	 * the more readable value of 'result-key'. 
	 * the 'callback' is used to apply the callback to the resultset
	 *
     * @param   array  $options
     * @return  DbRequestInterface
     */
    public function setMultiResultOptions(array $options);

	/**
	 * This is a flag used to tell the vendor db adapter to use buffered  a
	 * resultset. Note it is up to the database vendor to support this. 
	 * Adapters will ignore it when not supported
	 * 
	 * @return	bool
	 */
	public function isResultBuffer();

	/**
	 * @return	DbRequestInterface
	 */
	public function enableResultBuffer();
	
	/**
	 * Use this when dealing with large datasets so they are not buffered
	 *
	 * @return	DbRequestInterface
	 */
	public function disableResultBuffer();

	/**
	 * @return	mixed
	 */
	public function getCallback();

	/**
	 * A callback will be applied to every row in the resultset
	 * 
	 * 1) It is an InvalidArgumentException for $callback to not be callable
	 *	  is_callable must return true on this paramter
	 *
	 * @throws	InvalidArgumentException
	 * @param	mixed	$callback
	 * @return	DbRequestInterface
	 */
	public function setCallback($callback);

    /**
     * @return  array
     */
    public function getValues();
    
    /**
	 * Used in prepared sql, this holds the values to be bound
	 *
	 * 1) empty arrays are allowed
	 * 2) request type of prepared-stmt is not validated against
	 *
     * @param   array  $values
     * @return  DbRequestInterface
     */
    public function setValues(array $values);

    /**
     * @return bool
     */
    public function isValues();
}
