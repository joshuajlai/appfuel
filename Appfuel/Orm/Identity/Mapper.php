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
namespace Appfuel\Orm\Domain;

use BadMethodCallException,
	Appfuel\Framework\Exception;

/**
 * General mapping system the does simple lookups
 */
class Mapper implements IdentityMapperInterface
{
	/**
	 * Map key value pair
	 * @var	array
	 */
	protected $map = null;

	protected $closure = null;
	/**
	 * @param	array	map 
	 * @return	Mapper
	 */
	public function __construct(array $map)
	{
		$this->setMap($map);
	}

	public function getMapper()
	{
		return $this->closure;
	}
}
