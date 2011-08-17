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
	Appfuel\Framework\DataStructure\Dictionary,
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
	 * @param	string	$name	sql name used for datatype
	 * @param	int		$umax	unsigned max value
	 * @param	int		$min	signed min value
	 * @param	int		$max	signed max value
	 * @return	AbstractType
	 */
	public function __construct($name, $umax, $min, $max, array $attrs = null)
	{
		$this->setUmax($umax);
		$this->setMin($min);
		$this->setMax($max);

		/* default attributes when none are given */
		if (null === $attrs) {
			$attrs = array(
				'unsigned'		=> false,
				'is-nullable'	=> true,
			);
		}

		$attrs = new Dictionary($attrs);
		parent::__construct($name, $attrs);
	}

	/**
	 * @return	AbstractIntType
	 */
	public function enableUnsigned()
	{
		return $this->addAttribute('unsigned', true);
	}

	/**
	 * @return	AbstractIntType
	 */
	public function disableUnsigned()
	{
		return $this->addAttribute('unsigned', false);
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
