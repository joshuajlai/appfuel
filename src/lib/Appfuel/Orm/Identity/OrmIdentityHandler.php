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
namespace Appfuel\Orm\Identity;

use Closure,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Orm\Identity\IdentityHandlerInterface;

/**
 * Maps the domain members to a database table and columns
 */
class OrmIdentityHandler implements IdentityHandlerInterface
{
	/**
	 * Domain name used to refer to this domain so you don't have to use 
	 * the class name. Sometimes called the domain key but I am moving away
	 * from that term because it too similar to primary key which is not the
	 * meaning.
	 * @var string
	 */
	protected $domainName = null;

	/**
	 * This is the root namespace of all domains
	 * @var string
	 */
	protected $rootNamespace = null;

	/**
	 * Mappers are a list of named closures the perform a very specific
	 * mapping operation
	 * @var	array
	 */
	protected $mappers = array();

	/**
	 * @return string
	 */
	public function getDomainName()
	{
		return $this->domainName;
	}

	/**
	 * @return	OrmIdentityHandler
	 */
	public function setDomainName($label)
	{
		if (! $this->isString($label)) {
			throw new Exception("label must be a valid string");
		}

		$this->domainName = $label;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getRootNamespace()
	{
		return $this->rootNamespace;
	}

	/**
	 * @return	OrmIdentityHandler
	 */
	public function setRootNamespace($namespace)
	{
		if (! $this->isString($namespace)) {
			throw new Exception("namespace must be a valid string");
		}

		$this->rootNamespace = $namespace;
		return $this;
	}

	/**	
	 * @return
	 */
	public function addMapper($key, Closure $mapper)
	{
		if (! $this->isString($key)) {
			throw new Exception("key name for map must be a non empty string");
		}

		$this->mappers[$key] = $mapper;
		return $this;
	}

	/**
	 * @param	string	$key
	 * @return	Closure	| false on failure
	 */
	public function getMapper($key)
	{
		if (! $this->isMapper($key)) {
			return false;
		}

		return $this->mappers[$key];
	}

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	public function isMapper($key)
	{
		if (! $this->isString($key)) {
			return false;
		}

		return isset($this->mappers[$key]) && 
			   $this->mappers[$key] instanceof Closure;
	}

	/**
	 * Used by Concrete Identity handlers to load thier mappers
	 * 
	 * @return	null
	 */
	public function loadMaps()
	{}

	/**
	 * @param	mixed	$str
	 * @return	bool
	 */
	protected function isString($str)
	{
		return is_string($str) && ! empty($str);
	}
}
