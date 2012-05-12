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

use InvalidArgumentException,

/**
 * Common functionality for every orm domain model
 */
class DomainCollection implements DomainCollectionInterface
{
	/**
	 * Domain key is used to identify the domain class which is mapped in
	 * the registry.
	 * @var string
	 */
	private $domainKey = null;

	/**
	 * Used to create and marshel domain objects
	 * @var string
	 */	
	private $domainBuilder = null;

	/**
	 * Raw data used to lazy load the the domain objects. Domains are
	 * not created until they are needed
	 * @var	array
	 */
	private $raw = array();

	/**
	 * List of already created domains. These were either create from raw
	 * or added to the collection
	 * @var array
	 */
	private $objCache = array();

	/**
	 * Current position of array pointer
	 * @var int
	 */
	private $index = 0;

	/**
	 * The type of domain is immutable and can not be change during the 
	 * lifespan of the collection.
	 *
	 * @param	string	$type		class or interface of domain
	 * @return	DomainCollection
	 */
	public function __construct($key, DomainBuilderInterface $builder = null)
	{
		$this->setDomainKey($key);

		if (null === $builder) {
			$builder = new DomainBuilder();
		}
		$this->setDomainBuilder($builder);
	}

	/**
	 * @return	string
	 */
	public function getDomainKey()
	{
		return $this->domainKey;
	}
	
	/**
	 * @return	DomainBuilderInterface
	 */
	public function getDomainBuilder()
	{
		return $this->domainBuilder;
	}

	/**
	 * @return	int
	 */
	public function count()
	{
		return count($this->raw);
	}

	/**
	 * Make sure the index is back to zero restoring the array pointer back
	 * to the beginning 
	 *
	 * @return	null
	 */
	public function rewind()
	{
		$this->index = 0;
	}

	/**
	 * The domain at the current index
	 *
	 * @return	DomainModelInterface | false no domain exists
	 */
	public function current()
	{
		return $this->getRow($this->index);
	}

	/**
	 * @return	int
	 */
	public function key()
	{
		return $this->index;
	}

	/**
	 * @return	bool
	 */
	public function valid()
	{
		return $this->current() instanceof DomainModelInterface;
	}

	/**
	 * @return	null
	 */
	public function next()
	{
		if ($this->valid()) {
			$this->index++;
		}
	}

	/**
	 * @param	DomainModelInterface	$domain
	 * @return	null
	 */
	public function add(DomainModelInterface $domain)
	{	
		$count = $this->count();
		if (0 === $count) {
			$max = $count;
		}
		else {
			$max = $count++;
		}

		$this->raw[$max]      = '__NEW__';
		$this->objCache[$max] = $domain;
		return $this;
	}

	/**
	 * @param	array	$data
	 * @return	DomainCollection
	 */
	public function loadRawData(array $data)
	{
		$this->raw = $data;
		$this->rewind();
		$this->objCache = array();
		return $this;
	}

	/**
	 * @param	string	$method	
	 * @param	mixed	$value
	 * @return	DomainCollection | false
	 */
	public function searchByMethod($expected, $method, array $params = array())
	{
		if (empty($method) || ! is_string($method)) {
			return false;
		}

		$collection = $this->createEmptyCollection();
		foreach ($this as $domain) {
			$value = call_user_func_array(array($domain, $method),$params);
			if ($expected === $value) {
				$collection->add($domain);
			}
		}

		return $collection;
	}

	/**
	 * @return	DomainCollection
	 */
	protected function createEmptyCollection()
	{
		return new self($this->getDomainKey(), $this->getDomainBuilder());
	}

	/**
	 * Looks for a domain in the object chache otherwise looks for the domain
	 * in the raw data and then builds the domain and adds it to cache. When
	 * not found returns false
	 *
	 * @param	int	$index
	 * @return	DomainModelInterface | false when not found
	 */
	private function getRow($index)
	{
		$count = $this->count();
		if (! is_int($index) || $index >= $count || $index < 0) {
			return false;
		}

		/*
		 * domain found in object chache 
		 */
		if (isset($this->objCache[$index])) {
			return $this->objCache[$index];
		}

		if (isset($this->raw[$index])) {
			$builder = $this->getDomainBuilder();
			$key	 = $this->getDomainKey();
			$domain  = $builder->buildDomain($key, $this->raw[$index]);
			$this->objCache[$index] = $domain;
			return $domain;
		}

		return false;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string	$key
	 * @return	null
	 */
	private function setDomainKey($key)
	{
		if (empty($key) || ! is_string($key)) {
			$err = "domain key must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->domainKey = $key;
	}

	/**
	 * @param	mixed	$ns
	 * @return	null
	 */
	private function setDomainBuilder(DomainBuilderInterface $builder)
	{
		$this->domainBuilder = $builder;
	}
}
