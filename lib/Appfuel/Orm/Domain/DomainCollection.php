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

use Appfuel\Framework\Exception,
	Appfuel\Framework\Orm\Domain\OrmObjectFactoryInterface,
	Appfuel\Framework\Orm\Domain\DomainCollectionInterface;

/**
 * Common functionality for every orm domain model
 */
class DomainCollection implements DomainCollectionInterface
{
	/**
	 * Class or interface of the domain that belong to this collection. Only
	 * one type of domain is allowed in a collection
	 * @var string
	 */
	private $domainType = null;

	/**
	 * Raw data used to lazy load the the domain objects. Domains are
	 * not created until they are needed
	 * @var	array
	 */
	protected $raw = array();

	/**
	 * List of already created domains. These were either create from raw
	 * or added to the collection
	 * @var array
	 */
	protected $domains = array();

	/**
	 * The type of domain is immutable and can not be change during the 
	 * lifespan of the collection.
	 *
	 * @param	string	$type		class or interface of domain
	 * @return	DomainCollection
	 */
	public function __construct($type, $ns = null)
	{
		$this->setDomainType($type);
		$this->setObjectFactory($ns);
	}

	/**
	 * @return	string
	 */
	public function getDomainType()
	{
		return $this->domainType;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string	$type
	 * @return	null
	 */
	private function setDomainType($type)
	{
		if (empty($type) || ! is_string($type)) {
			throw new Exception("domain type must be a non empty string");
		}

		$this->domainType = $type;
	}

	/**
	 * @param	mixed	$ns
	 * @return	null
	 */
	private function setObjectFactory($ns = null)
	{
		if ($ns === null) {
			$ns = 'Appfuel\Domain';
		}

		if (is_string($ns)) {
			$factory = new OrmObjectFactory($ns);
		}
		else if ($ns instanceof OrmObjectFactoryInterface) {
			$factory = $ns;
		}
		else {
			$err  = "setObjectFactory will take a string that is the root ";
			$err .= "namespace of the domains for this collection or an ";
			$err .= "object that implments Appfuel\Framework\Orm\Domain";
			$err .= "\ObjectFactoryInterface";
			throw new Exception($err);
		}
	}
}
