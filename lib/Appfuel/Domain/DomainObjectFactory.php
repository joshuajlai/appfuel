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
namespace Appfuel\Orm\Domain;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Orm\Domain\ObjectFactoryInterface,
	Appfuel\Framework\Orm\Domain\MappedObjectNotFoundException;

/**
 * The object factory is resposible for converting domain keys into domain 
 * objects. It is not responsible for marshalling data into those objects.
 * Domain key mappings are handled by the static DomainRegistry
 */
class OrmObjectFactory implements ObjectFactoryInterface
{
	/**
	 * The location of the domains to be created. This allows the domains
	 * to be mapped in configuation files relative to this namespace
	 * @var	string
	 */
	protected $domainNs = null;
	
	/**
	 * @param	string	$domainNs
	 * @return	OrmObjectFactory
	 */
	public function __construct($domainNs)
	{
		$this->domainNs = $domainNs;
	}

	/**
	 * @return	string
	 */
	public function getDomainNamespace()
	{
		return $this->domainNs;
	}

	/**
	 * Create a domain model based on the naming convention 
	 * <parent-dir-name>Model. When isDomain is false then the mapped namespace
	 * will be treated as the fully qualified namespace with no naming 
	 * convention applied.
	 *
	 * @param	string	$key		key used to map the domain namespace
	 * @param	bool	$isDomain	flag used to determine if the naming 
	 *								convention should be applied
	 * @return	mixed
	 */
	public function createDomainObject($key)
	{
		$domainNs = $this->getDomainNs();
		$relative = DomainRegistry::getClass($key);
		if (false === $relative) {
			return false;
		}

		$domainClass = "{$domainNs}\\{$relative}";
		try {
			return new $domainClass();
		} catch (\Exception $e) {
			$err = "object not found for ($key) at ($domainClass)";
			throw new MappedObjectNotFoundException($err, 0, $e);
		}
	}
}
