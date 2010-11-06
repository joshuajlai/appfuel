<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace Appfuel\StdLib\AfIterator;

/**
 * Basic Iterator
 * This is a basic iterator that wraps the php native next(), current(), key()
 * functions. Valid is determined by the return value of key(). 
 * 
 * @package		Appfuel
 * @category	Stdlib
 * @subcategory	AfIterator
 */
class Basic implements \Countable, \Iterator
{
    /**
     * Data
     * Holds the elements of the list
     * @var array
     */
    private $data = array();

    /**
     * 
     * @param   array   $data       config data
     * @param   bool    $modify     determines if the config can be modified
     * @return  Config
     */
    public function __construct(array $data = array()) 
    {
		if (! empty($data)) {
			$this->load($data);
		} 
    }

    /**
     * Defined by Countable interface
     *
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * Iterator implementation that return array item in $_data
     * 
     * @return  mixed
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * Iterator implementation that returns the index element of the current 
     * position in $_data
     * 
     * @return mixed
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * Iterator implementation that advances the internal array pointer of 
     * $_data and increments index
     *
     * @return void
     */
    public function next()
    {
        return next($this->data);
    }

    /**
     * Iterator implementation that moves the internal array pointer of
     * $_data to the beginning
     *
     * @return void
     */
    public function rewind()
    {
        return reset($this->data);
    }

    /**
     * Iterator implementation that checks if index is in range
     * I choose empty because the return value for key() when the internal
	 * array pointer has passed the end of the array is NULL and an
	 * empty string is an invalid key
	 * 
     * @return  bool
     */
    public function valid()
    {
        $key = $this->key();
		return is_string($key) && strlen($key) > 0;
    }

    /**
     * @param   array   $data   config data
     * @param   bool    $modify can this data be changed
     * @return  void
     */
    public function load(array $data)
    {
		foreach ($data as $key => $value) {
            $this->add($key, $value);
        }

		return $this;
    }

    /**
     * @param   string  $key        label for config data item
     * @param   mixed   $value      config item
     * @param   bool    $readOnly   determine if this item can change
     * @return  void
     */
    public function add($key, $value)
    {
		if (! is_scalar($key)) {
			throw new Exception("Key must be a string");
		}

		$this->data[$key] = $value;
		return $this;
    }
}
