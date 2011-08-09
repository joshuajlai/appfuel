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
	Appfuel\Framework\Orm\Domain\DataBuilderInterface,
	Appfuel\Framework\Orm\Domain\ObjectFactoryInterface;

/**
 * The object factory is resposible for converting domain keys into domain 
 * objects. It is not responsible for marshalling data into those objects.
 */
class DataBuilder implements DataBuilderInterface
{
	/**
	 * Object Factory is used to build domain objects based on domain keys
	 * @var ObjectFactoryInterface
	 */
	protected $objFactory = null;

	
	/**
	 * @param	ObjetFactoryInteface
	 * @return	DataBuilder
	 */
	public function __construct(ObjectFactoryInterface $factory)
	{
		$this->objFactory = $factory;
	}

	/**
	 * @return	ObjectFactoryInterface
	 */
	public function getObjectFactory()
	{
		return $this->objFactory;
	}

	/**
	 * @param	string	$key
	 * @return	mixed
	 */
	public function buildDomainModel($key, array $data = null, $isNew = false)
	{
		if (empty($key) || ! is_string($key)) {
			return false;
		}
	
		$isNew	 = (bool)$isNew;
		$err	 = "buildDomainModel failed: ";
		$factory = $this->getObjectFactory();
		$domain  = $factory->createDomainObject($key);
		if (! $domain) {
			$err .= "domain key ($key) is not mapped";
			throw new Exception($err);
		}

		$domain->_marshal($data);
		if ($isNew) {
			$domain->_markNew();
		}

		return $domain;
	}
}
