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

use Exception,
	RunTimeException,
	Appfuel\Kernel\KernelRegistry;

/**
 * The domain builder works in calobaration with the registry to allow
 * developers to referer to domains by a domain-key and not the class name.
 * the builder does a lookup in the registry for the domain class based on the
 * the key.
 */
class DomainBuilder implements DomainBuilderInterface
{
	/**
	 * @param	string	$key	domain key
	 * @param	array	$data	null
	 * @param	bool	$isNew	flag used to change the domain state
	 * @return	DomainInterface | false		when not found
	 */
	public function buildDomain($key, array $data = null, $isNew = false)
	{
        if (empty($key) || ! is_string($key)) {
            return false;
        }

		$domain = $this->createDomainObject($key);
		if (! ($domain instanceof DomainModelInterface)) {
			return false;
		}

		if ($data !== null) {
			$domain->_marshal($data);
		}

		if (true === $isNew) {
			$domain->_markNew();
		}
		
		return $domain;
	}
 
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
		$domainClass = KernelRegistry::getDomainClass($key);
		if (false === $domainClass) {
			return false;
		}

		try {
			return new $domainClass();
		} catch (Exception $e) {
			$err = "object not found for ($key) at ($domainClass)";
			throw new RunTimeException($err, 0, $e);
		}
	}
}
