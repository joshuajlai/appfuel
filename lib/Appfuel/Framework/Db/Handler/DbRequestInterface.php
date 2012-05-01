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
namespace Appfuel\Framework\Db\Handler;

/**
 * Used Primarly by the DbHandlerInterface. This holds all information needed
 * to send a sql command to the database and parse the results. 
 */
interface DbRequestInterface
{
	/**
	 * Type of request read|write|both|ignore
	 *
	 * @return string
	 */
	public function getServerMode();
	
	/**
	 * @param	string	$type	read|write|both|ignore
	 *
	 * @return	RequestInterface
	 */
	public function setServerMode($type);

	/**
	 * Alias for setServerMode('read')
	 *
	 * @return	RequestInterface
	 */
	public function enableReadOnly();
	
	/**
	 * Alias for setServerMode('write')
	 *
	 * @return	RequestInterface
	 */
	public function enableWrite();

	/**
	 * Alias for setServerMode('ignore')
	 *
	 * @return	RequestInterface
	 */
	public function ignoreServerMode();

	/**
	 * @param	string	$type	query|mult-query|prepared-stmt
	 * @return	RequestInterface
	 */
	public function setRequestType($type);

	/**
	 * @param	string
	 */
	public function getRequestType();
	
	/**
	 * Alias for setRequestType('query'). Should be the defualt
	 * request type if no other type is given.
	 * 
	 * @return	RequestInterface
	 */
	public function enableMultiQuery();

	/**
	 * Alias for setRequestType('multi-query')
	 *
	 * @return	RequestInterface
	 */
	public function enableQuery();
	
	/**
	 * Alias for setRequest('prepared-stmt')
	 *
	 * @return	RequestInterface
	 */
	public function enablePreparedStmt();
	
	/**
	 * Sql being sent to the database
	 * 
	 * @return string
	 */
	public function getSql();
	
	/**
	 * @param	string
	 * @return	RequestInterface
	 */
	public function setSql($sql);

    /**
     * Used only in multi-query request this will add a sql string to the 
	 * existsing sql strings
     * 
     * @param   string  $sql
     * @return  RequestInterface
     */
    public function addSql($sql);

    /**
	 * Used only in Multi Query Request this will load a list of individual
	 * sql statements
	 *
     * @param   array   $list
     * @return  RequestInterface
     */
    public function loadSql(array $list);
    
    /**
	 * Determines if a non empty string has been set. Does not care if that
	 * string happens to be valid sql.
	 *
     * @return  bool
     */
    public function isSql();

	/**
	 * @return	string
	 */
	public function getResultType();

	/**
	 * This is the array structure of the resultset from the database. It has
	 * three values name, position, both. Name gives you an associative array
	 * of column names, position gives you an array where the array key is
	 * the ordinal position of the column in the table, both is an array with
	 * both position and name. 
	 *
	 * @param	string	$type	name|position|both
	 * @return	RequestInterface
	 */
	public function setResultType($type);

	/**
	 * Enable buffering, used for large datasets
	 * 
	 * @return	RequestInterface
	 */
	public function enableResultBuffer();

	/**
	 * @return	RequestInterface
	 */
	public function disableResultBuffer();
	
	/**
	 * @return	bool
	 */
	public function isResultBuffer();
	
	/**
	 * @return	mixed
	 */
	public function getCallback();
	
	/**
	 * Closure or callback to which is applied to each row of the dataset
	 * the is returned by the database. The results of the row change are
	 * saved back to the dataset
	 * 
	 * @param	mixed	$callback
	 * @return	RequestInterface
	 */
	public function setCallback($callback);

	/**
	 * @return	array
	 */
    public function getValues();

	/**
	 * Used mainly for prepared statements which hold the values marked by
	 * the prepared symbol ?
	 *
	 * @param	array	$values
	 * @return	RequestInterface
	 */
    public function setValues(array $values);

	/**
	 * Used by prepared statements to determines if any values exist
	 * 
	 * @return	bool
	 */
	public function isValues();

    /**
	 * Used in a multiQuery request the options that map to each resultset. 
	 * Options include:
     *  resultKey - replaces the number index with resultKey
     *  callback  - filters each row of the result with this callback
     */
    public function setResultOptions(array $options);

	/**
	 * @return	array
	 */
	public function getResultOptions();
}
