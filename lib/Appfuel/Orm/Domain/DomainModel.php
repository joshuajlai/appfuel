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
	Appfuel\Framework\Exception,
	Appfuel\Framework\Orm\Domain\DomainStateInterface,
	Appfuel\Framework\Orm\Domain\DomainModelInterface;

/**
 * Common functionality for every orm domain model
 */
abstract class DomainModel implements DomainModelInterface
{
	/**
	 * Holds the internal state of the domain. Domain states include:
	 * marshal	: domain was built from the datasource
	 * new		: domain needs to be added to the datasource
	 * delete   : domain needs to be removed from the datasource
	 * dirty	: domain has changed and needs to be updated in the datasource
	 * @var	DomainState
	 */
	private $state = null;

	/**
	 * Marshalling is acting of building a domain from the datasource. Every
	 * domain has the ability internally marshal already mapped data into its
	 * member variables. This strict flag determines if the domain will throw
	 * an exception or not
	 * @var bool
	 */
	private $isStrictMarshal = false;

	/**
	 * @return DomainState
	 */
	public function getDomainState()
	{
		return $this->state;
	}

	/**
	 * @param	DomainState $state
	 * @return	DomainModel
	 */
	public function setDomainState(DomainStateInterface $state)
	{
		$this->state = $state;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isStrictMarshalling()
	{
		return $this->isStrictMarshal;
	}

	/**
	 * @return	DomainModel
	 */
	public function enableStrictMarshalling()
	{
		$this->isStrictMarshal = true;
		return $this;
	}

	/**
	 * @return	DomainModel
	 */
	public function disableStrictMarshalling()
	{
		$this->isStrictMarshal = false;
		return $this;
	}

	/**
	 * Marshal the datasource values into the domain members and updata the 
	 * state object
	 *
	 * @param	array	$data	member name names and values 
	 * @return	DomainModel
	 */
	public function marshal(array $data = null)
	{
		if (! $this->isDomainState()) {
			$this->loadNewDomainState();
		}
		$state = $this->getDomainState();
		$state->markMarshal($data);

		if (empty($data)) {
			return $this;
		}
		
		$isStrict = $this->isStrictMarshalling();
		$err = "Failed domain marshal: ";
		foreach ($data as $member => $value) {

			$setter = 'set' . ucfirst($member);
			if (method_exists($this, $setter)) {
				try {
					$this->$setter($value);
				} catch (\Exception $e) {
					$err .= "invalid argument for ($setter)";
					throw new BadMethodCallException($err, null, $e);
				}
				continue;
			}

			if ($isStrict) {
				throw new BadMethodCallException(
					"$err ($setter) does not exist"
				);
			}
		}

		return $this;
	}
	
	/**
	 * @return	DomainModel
	 */
	public function markNew()
	{
		if (! $this->isDomainState()) {
			$this->loadNewDomainState();
		}

		$state = $this->getDomainState();
		$state->markNew();
		
		return $this;
	}

	/**
	 * @param	string	$member
	 * @return	DomainModel
	 */
	public function markDirty($member)
	{
		if (! property_exists($this, $member)) {
			throw new Exception("invalid markDirty ($member) does not exist");
		}

		if (! $this->isDomainState()) {
			$this->loadNewDomainState();
		}

		$this->getDomainState()
			 ->markDirty($member);

		return $this;
	}

	/**
	 * @return	DomainModel
	 */
	public function markDelete()
	{
		if (! $this->isDomainState()) {
			$this->loadNewDomainState();
		}

		$this->getDomainState()
			 ->markDelete();
		
		return $this;
	}

	/**
	 * @param	string	$member 
	 * @return	DomainModel
	 */
	public function markClean($member = null)
	{
		/* no need to clear when there is no state */
		if (! $this->isDomainState()) {
			return $this;
		}

		$this->getDomainState()
			 ->markClean($member);

		return $this;
	}

	/**
	 * @return bool
	 */
	private function isDomainState()
	{
		return $this->state instanceof DomainStateInterface;
	}

	/**
	 * @return null
	 */
	private function loadNewDomainState()
	{
		$this->setDomainState(new DomainState());
	}
}
