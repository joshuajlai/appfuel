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
	Appfuel\Framework\Db\Request\MultiQueryRequestInterface;


/**
 * Functionality needed to handle database request for queries
 */
class MultiQueryRequest 
	extends QueryRequest 
		implements MultiQueryRequestInterface
{
	/**
	 * Used to determine which adapter will service this request
	 * @var string
	 */
	protected $code = 'multiquery';

	/**
	 * Holds the options that map to each resultset. Options include:
	 *	resultKey - replaces the number index with resultKey
	 *	callback  - filters each row of the result with this callback
	 * @var array
	 */
	protected $resultOptions = array();

	/**
	 * @param	string	$type
	 * @param	mixed	array|string|null
	 * @return	MultiQueryRequest
	 */
	public function __construct($type, $sql = null)
	{
		$this->setType($type);
		if (null === $sql) {
			return;
		}

		if (is_array($sql)) {
			$this->loadSql($sql);
		}
		else if ($this->isValidString($sql)) {
			$this->setSql($sql);
		}
		else {
			$err = "must be an array of strings or a string";
			throw new Exception("Invalid sql: $err");
		}
	}

	/**
	 * Add a sql string to the existsing sql strings
	 * 
	 * @param	string	$sql
	 * @return	MultiQueryRequest
	 */
	public function addSql($sqlStr)
	{
		if (! $this->isValidString($sqlStr)) {
			throw new Exception("Invalid sql: must be non empty string");
		}

		if (! $this->isSql()) {
			$this->setSql($sqlStr);
			return $this;
		}

		$this->setSql("{$this->getSql()};$sqlStr");
		return $this;
	}

	/**
	 * @param	array	$sqlList
	 * @return	MultiQueryRequest
	 */
	public function loadSql(array $sqlList)
	{
		foreach ($sqlList as $sqlStr) {
			$this->addSql($sqlStr);
		}

		return $this;
	}
	
	/**
	 * @return	bool
	 */
	public function isSql()
	{
		return $this->isValidString($this->sql);
	}

	/**
	 * @return	string
	 */
	public function getResultOptions()
	{
		return $this->resultOptions;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string	$type
	 * @return	RequestQuery
	 */
	public function setResultOptions(array $options)
	{
		$this->resultOptions = $options;
		return $this;
	}
}
