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

use Appfuel\Framework\Exception,
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
	 * @param	mixed	$str
	 * @return	bool
	 */
	protected function isString($str)
	{
		return is_string($str) && ! empty($str);
	}
}
