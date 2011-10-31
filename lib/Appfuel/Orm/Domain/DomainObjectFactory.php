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
	Appfuel\Framework\Registry,
	Appfuel\Framework\Orm\Domain\DomainObjectFactoryInterface,
	Appfuel\Framework\Orm\Domain\MappedObjectNotFoundException;

/**
 * The object factory is resposible for converting domain keys into domain 
 * objects. It is not responsible for marshalling data into those objects.
 * Domain key mappings are handled by the static DomainRegistry
 */
class DomainObjectFactory implements DomainObjectFactoryInterface
{
	/**
	 * Since the domain class is decoupled by the its key we must retrieve
	 * the domain class form the domain registry that holds the mapping. Once
	 * we have the class use the root domain namespace 
	 *
	 * @param	string	$key	key used to map the domain namespace
	 * @return	mixed
	 */
	public function createDomainObject($key)
	{
		$domainClass = Registry::getDomainClass($key);
		if (false === $domainClass) {
			return false;
		}

		try {
			return new $domainClass();
		} catch (\Exception $e) {
			$err = "object not found for ($key) at ($domainClass)";
			throw new MappedObjectNotFoundException($err, 0, $e);
		}
	}
}
