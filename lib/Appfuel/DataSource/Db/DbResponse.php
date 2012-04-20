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

use InvalidArgumentException,
	Appfuel\Error\ErrorStack,
	Appfuel\Error\ErrorInterface,
	Appfuel\Error\ErrorStackInterface;

/**
 * A vendor agnostic object that encapsulates details about the database
 * execution by a vendor specific query adapter. It holds the result 
 * (multi query results can hold an array of DbResponses). Holds a list 
 * errors from the database
 */
class DbResponse implements DbResponseInterface
{
	/**
     * Flag used to determine if the sql operation was successful, mostly by writes
	 * @var bool
	 */
	protected $status = null;

	/**
	 * Holds the information requested from the database
	 * @var array
	 */
	protected $results = array();

	/**
	 * Error object holding all error info about the query just issued
	 * @var	Error
	 */
	protected $errorStack = null;

    /**
     * Number of rows affected by the sql operation that generated this response
     * @var int
     */
    protected $affected = 0;

	/**
	 * @param	ErrorStackInterface	$stack
	 * @return	DbResponse
	 */
	public function __construct(ErrorStackInterface $stack = null)
	{
		if ($stack === null) {
			$stack = new ErrorStack();
		}
		$this->errorStack = $stack;
	}

    /**
     * @return  bool
     */
    public function markSuccessful()
    {
        $this->status = true;
        return $this;
    }

    /**
     * @return  bool
     */
    public function markFailure()
    {
        $this->status = false;
        return $this;
    }

	/**
	 * @return	bool | null when not used
	 */
	public function getStatus()
	{
		return $this->status;
	}

    /**
     * @return  bool
     */
    public function isSuccessful()
    {
        return true === $this->status;
    }

    /**
     * @return  bool
     */
    public function isFailure()
    {
        return false === $this->status;
    }

	/**
	 * @param	bool	$result
	 * @return	DbResponse
	 */
	public function setAffectedRows($count)
	{
        if (false === $count || null === $count || $count < 0) {
            $this->markFailure();
            $this->affected = -1;
        }
        else {
            $this->markSuccessful();
            $this->affected = $rows;
        }
        
		return $this;
	}

    /**
     * @return  int
     */
    public function getAffectedRows()
    {
        return $this->affected;
    }

	/**
	 * @return	ErrorStackInterface
	 */
	public function getErrorStack()
	{
		return $this->errorStack;	
	}

	/**
	 * @return bool
	 */
	public function isError()
	{
		return $this->getErrorStack()
					->isError();
	}

	/**
	 * @return	Error
	 */
	public function getError()
	{
		return $this->getErrorStack()
					->current();
	}

	/**
	 * @param	int		$code
	 * @param	string	$msg
	 * @return	DbResponse
	 */
	public function addError($msg, $code = 500)
	{
		$stack = $this->getErrorStack();
		if ($msg instanceof ErrorInterface) {
			$stack->addErrorItem($msg);
		}
		else {
			$stack->addError($msg, $code);
		}
	
		if ($this->isSuccessful()) {
			$this->markFailure();
		}

		return $this;
	}

	/**
	 * @return	int | false on failure
	 */
	public function count()
	{
		return count($this->results);
	}

	/**
	 * The current resultset
	 *
	 * @return	array | DbResponseInterface
	 */
	public function current()
	{
		return current($this->results);
	}

	/**
	 * @return	scalar | null when no key exists
	 */
	public function key()
	{
		return key($this->results);
	}

	/**
	 * Advance the pointer to the next result
	 *
	 * @return	null
	 */
	public function next()
	{
		next($this->results);
	}	

	/**
	 * Advance the pointer to the beginning of the resultset
	 *
	 * @return	null
	 */
	public function rewind()
	{
		reset($this->results);
	}

	/**
	 * Determine if the item at the key is any array (a database resultset) or
	 * a DbResponseInterface (happens with multi queries)
	 *
	 * @return	bool
	 */
	public function valid()
	{
		$key = $this->key();
		if (null === $key || ! isset($this->results[$key])) {
			return false;
		}

		$result = $this->results[$key];
		if (! array($result) && ! ($result instanceof DbResponseInterface)) {
			return false;
		}
		return true;
	}

	/**
	 * The full resultset. On a mult query this is an array of 
	 * DbResponseInterface objects
	 *
	 * @return	array
	 */
	public function getResultSet()
	{
		return $this->results;
	}

	/**
	 * @param	array	$results
	 * @return	DbResponse
	 */	
	public function setResultSet(array $results)
	{
		$this->results = $results;
		return $this;
	}

	/**
	 * @param	scalar	$key	
	 * @return	mixed	array | DbResponseInterface | null
	 */
	public function getResult($key)
	{	
		if (is_scalar($key) && array_key_exists($key, $this->results)) {
			return $this->results[$key];
		}

		return null;
	}


	/**
	 * Add a single result to the resultset. When key is not includes then it
	 * takes the count as the index number.
	 *
	 * @param	mixed array|DbResponseInterface $result
	 * @param	scalar	$key	optional
	 * @return	DbResponse
	 */
	public function addResult($result, $key = null)
	{
		$err = 'addResult failed: ';
		if (! is_array($result) && ! ($result instanceof DbResponseInterface)) {
			$err .= 'must be an array or an object that implements';
			$err .= 'Appfuel\Db\DbResponseInterface';
			throw new InvalidArgumentException($err);
		}

		if (null === $key) {
			$key = $this->count();
		}

		if (is_string($key)) {
			$key = trim($key);
		}

		if (! is_int($key) && (! is_string($key) || strlen($key) === 0)) {
			$err .= 'result key must be an integer or a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->results[$key] = $result;
		return $this;
	}
}
