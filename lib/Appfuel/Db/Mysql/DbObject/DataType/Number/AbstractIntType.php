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
namespace Appfuel\Db\Mysql\DbObject\DataType\Number;

use Appfuel\Framework\Exception,
	Appfuel\Db\Mysql\DbObject\DataType\AbstractType;

/**
 * Handle logic common to all integer types
 */
abstract class AbstractIntType extends AbstractType
{
	/**
	 * Unsigned max value
	 * @var int
	 */
	protected $umax = null;
	
	/**
	 * Signed min value
	 * @var int
	 */
	protected $min  = null;
	
	/**
	 * Signed max value
	 * @var int
	 */
	protected $max  = null;

	/**
	 * Flag used to determine if this int type is unsigned
	 * @var bool
	 */
	protected $isUnsigned = false;


	/**
	 * @param	string	$name	sql name used for datatype
	 * @param	int		$umax	unsigned max value
	 * @param	int		$min	signed min value
	 * @param	int		$max	signed max value
	 * @return	AbstractType
	 */
	public function __construct($name, $umax, $min, $max, $isUnsigned = false)
	{
		$this->setUmax($umax);
		$this->setMin($min);
		$this->setMax($max);
		$this->isUnsigned =(bool) $isUnsigned;
		parent::__construct($name);
	}

	/**
	 * @return	AbstractIntType
	 */
	public function enableUnsigned()
	{
		$this->isUnsigned = true;
		return $this;
	}

	/**
	 * @return	AbstractIntType
	 */
	public function disableUnsigned()
	{
		$this->isUnsigned = false;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isUnsigned()
	{
		return $this->isUnsigned;
	}

	/**
	 * @return	int
	 */
	public function getUmin()
	{
		return 0;
	}

	/**
	 * @return	int
	 */
	public function getUmax()
	{
		return $this->umax;
	}

	/**
	 * @return	int
	 */
	public function getMin()
	{
		return $this->min;
	}

	/**
	 * @return	int
	 */
	public function getMax()
	{
		return $this->max;
	}

	/**
	 * @param	int		$umax
	 * @return	null
	 */
	protected function setUmax($umax)
	{
		if (! is_int($umax) || $umax < 0) {
			throw new Exception("umax must be a int greater than zero");
		}

		$this->umax = $umax;
	}

	/**
	 * @param	int		$umax
	 * @return	null
	 */
	protected function setMin($min)
	{
		if (! is_int($min) || $min > 0) {
			throw new Exception("signed min must be a int less than zero");
		}

		$this->min = $min;
	}

	/**
	 * @param	int		$max
	 * @return	null
	 */
	protected function setMax($max)
	{
		if (! is_int($max) || $max < 0) {
			throw new Exception("signed max must be a int greater than zero");
		}

		$this->max = $max;
	}



}
