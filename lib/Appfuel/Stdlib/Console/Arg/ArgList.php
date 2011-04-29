<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace 	Appfuel\StdLib\Console\Arg;

use Appfuel\Stdlib\Data\Bag,
	Appfuel\Stdlib\Data\BagInterface;

/**
 * Datastructure used to hold and retrieve the command line arguments of
 * a program.
 */
class ArgList implements \Countable
{
	/**
	 * Long options used on the command line are expressed as 
	 * --option or --option=value
	 */
	protected $long = NULL;
	
	/**
	 * Short options used on the command line are expressed as
	 * -a or -a=value or grouped togather as -abc (-a -b -c)
	 */
	protected $short = NULL;

	/**
	 * arguments are regular arguements with no interpretation like
	 * foo bar baz 
	 */
	protected $args = array();

	/**
	 * @return	ArgList
	 */
	public function __construct()
	{
		$this->long  = new Bag();
		$this->short = new Bag();
		$this->args  = array();
	}

	/**
	 * @param	string	$key
	 * @param	mixed	$value
	 * @return	ArgList
	 */
	public function addLongOption($key, $value = NULL)
	{
		if (is_scalar($key)) {
			$this->long->add($key, $value);
		}

		return $this;
	}

	/**
	 * @param	scalar	$key
	 * @return	mixed
	 */
	public function getLongOption($key)
	{
		return $this->long->get($key);
	}

	/**
	 * @param	scalar	$key
	 * @return	bool
	 */
	public function isLongOption($key)
	{
		return $this->long->exists($key);
	}

    /**
     * @param   scalar  $key
     * @param   mixed   $value
     * @return  ArgList
     */
	public function addShortOption($key, $value = NULL)
	{
		if (is_scalar($key) && strlen($key) === 1) {
			$this->short->add($key, $value);
		}

		return $this;
	}

	/**
     * @param   string  $key
     * @return  mixed
     */
	public function getShortOption($key)
	{
		return $this->short->get($key);
	}

    /**
     * @param   string  $key
     * @return  bool
     */
	public function isShortOption($key)
	{
		return $this->short->exists($key);
	}

    /**
     * @param   mixed  $arg
     * @return  ArgList
     */
	public function addArg($arg)
	{
		if (is_scalar($arg)) {
			$this->args[] = $arg;
		}

		return $this;
	}

    /**
     * @param   string  $arg
     * @return  bool
     */
	public function isArg($arg)
	{
		return in_array($arg, $this->args);
	}

    /**
     * @return  array
     */
	public function getArgs()
	{
		return $this->args;
	}

	/**
	 * Total count of options (long and short) and arguments
	 * 
	 * @return int
	 */
	public function count()
	{
		return $this->countLong() + 
			   $this->countShort() + 
			   $this->countArg();
	}

	/**
	 * Total count of long options
	 * 
	 * @return int
	 */
	public function countLong()
	{
		return $this->long->count();

	}

	/**
	 * Total count of short options
	 * 
	 * @return int
	 */
	public function countShort()
	{
		return $this->short->count();

	}

	/**
	 * Total count of plain arguments
	 * 
	 * @return int
	 */
	public function countArg()
	{
		return count($this->args);

	}
}

