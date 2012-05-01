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
namespace Appfuel\Http;


use Countable,
	Iterator,
	InvalidArgumentException;

/**
 * Manages a list of http headers
 */
class HttpHeaderList implements HttpHeaderListInterface, Countable, Iterator
{
	/**
	 * List of http headers
	 * @var string
	 */
	protected $headers = array();

	/**
	 * @return	int
	 */
	public function count()
	{
		return count($this->headers);
	}

    /**
     * The domain at the current index
     *
     * @return  DomainModelInterface | false no domain exists
     */
    public function current()
    {  
        return current($this->headers);
    }

    /**
     * @return  int
     */
    public function key()
    {  
        return key($this->headers);
    }

    /**
     * @return  bool
     */
    public function valid()
    {
		$key = $this->key();
		if (! isset($this->headers[$key])	|| 
			empty($this->headers[$key])		|| 
			! is_string($this->headers[$key])) {
			return false;
		}

		return true;
    }

    /**
     * @return  null
     */
    public function next()
    {  
        if ($this->valid()) {
            next($this->headers);
        }
    }

	public function rewind()
	{
		reset($this->headers);
	}

	/**
	 * Returns the current header in the list
	 *
	 * @return	null
	 */
	public function getHeader()
	{
		return $this->current();
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$header
	 * @return	null
	 */
	public function addHeader($header)
	{
		if (empty($header) || 
			!is_string($header) || !($header = trim($header))) {
			$err = "header must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		if ($this->isHeader($header)) {
			return false;
		}

		$this->headers[] = $header;
		return true;
	}

	/**
	 * @param	array	$headers
	 * @return	null
	 */
	public function loadHeaders(array $headers)
	{
		foreach ($headers as $header) {
			$this->addHeader($header);
		}

		$this->rewind();
	}

	/**
	 * @return	return	array
	 */
	public function getAllHeaders()
	{
		return $this->headers;
	}

	/**
	 * case insensitive search through the headers
	 * 
	 * @param	string	$header
	 * @return	bool
	 */
	public function isHeader($header)
	{
		if (empty($header) || ! is_string($header)) {
			$err = "header must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$header = strtolower($header);
		foreach ($this->headers as $item) {
			if (strtolower($item) === $header) {
				return true;
			}
		}

		return false;
	}
}
