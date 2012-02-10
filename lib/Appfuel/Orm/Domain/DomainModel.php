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

use RunTimeException,
	BadMethodCallException,
	InvalidArgumentException;

/**
 * Common functionality for every orm domain model
 */
abstract class DomainModel implements DomainModelInterface
{
	/**
	 * Domain Id 
	 * @var	mixed	string|int
	 */
	protected $id = null;

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
	 * an exception or not when the member does not exist
	 * @var bool
	 */
	private $isStrictMarshal = true;

	/**
	 * Assign the domain state and id if it exists
	 *
	 * @param	scalar	$id	
	 * @return	DomainModel
	 */
	public function __construct($id = null)
	{
		if ($id !== null) {
			$this->setId($id);
		}
		$this->_setDomainState(new DomainState());
	}

	/**
	 * return	scalar
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param	scalar	$id
	 * @return	DomainModel
	 */
	public function setId($id)
	{
		if (! is_scalar($id)) {
			throw new InvalidArgumentException(
				"Domain id must be a scalar value"
			);
		}
		$this->id = $id;
		return $this;
	}

	/**
	 * Basic automation for getter and setter support 
	 * The naming convention follows camelCase so to determine which member
	 * this call is for we split the string into two parts and lower case the
	 * first character in the second part that represents the member variable
	 *
	 * @param	string 
	 */
	public function __call($name, array $args)
	{
		$prefix = substr($name, 0, 3);
		$member = substr($name, 3);
		$member{0} = strtolower($member{0});
		
		if ('set' === $prefix) {
			$this->_markDirty($member);
			$this->$member = $args[0];
			return $this;
		}
		
		if ('get' === $prefix) {
			return $this->$member;
		}
	}

	/**
	 * @return DomainState
	 */
	public function _getDomainState()
	{
		return $this->state;
	}

	/**
	 * @param	DomainState $state
	 * @return	DomainModel
	 */
	public function _setDomainState(DomainStateInterface $state)
	{
		$this->state = $state;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function _isStrictMarshalling()
	{
		return $this->isStrictMarshal;
	}

	/**
	 * @return	DomainModel
	 */
	public function _enableStrictMarshalling()
	{
		$this->isStrictMarshal = true;
		return $this;
	}

	/**
	 * @return	DomainModel
	 */
	public function _disableStrictMarshalling()
	{
		$this->isStrictMarshal = false;
		return $this;
	}

	/**
	 * Marshal the datasource values into the domain members and updata the 
	 * state object. This relys on one of two assumptions to be true:
	 * 1) when the setter does not exist the original __call is executed
	 *	  to automate the setting of the member
	 * or 
	 * 
	 * 2) the developer overrides the __call with their own setter. 
	 *
	 * @param	array	$data	member name names and values 
	 * @return	DomainModel
	 */
	public function _marshal(array $data = null)
	{
		$state = $this->_getDomainState();
		$state->markMarshal($data);

		if (empty($data)) {
			return $this;
		}
		
		$isStrict = $this->_isStrictMarshalling();
		$err = "Failed domain marshal:";
		foreach ($data as $member => $value) {
			$setter = 'set' . ucfirst($member);

			try {
				$this->$setter($value);
			} catch (Exception $e) {
				if ($isStrict) {
					$msg = $e->getMessage();
					throw new RunTimeException("$err $setter: $msg", null, $e);
				}
			}
		}

		$this->_markClean();
		return $this;
	}
	
	/**
	 * @return	DomainModel
	 */
	public function _markNew()
	{
		$this->_getDomainState()
	         ->markNew();
		
		return $this;
	}

	/**
	 * @param	string	$member
	 * @return	DomainModel
	 */
	public function _markDirty($member)
	{
		if (! property_exists($this, $member)) {
			throw new InvalidArgumentException(
				"invalid markDirty ($member) does not exist"
			);
		}

		$this->_getDomainState()
			 ->markDirty($member);

		return $this;
	}

	/**
	 * @return	DomainModel
	 */
	public function _markDelete()
	{
		$this->_getDomainState()
			 ->markDelete();
		
		return $this;
	}

	/**
	 * @param	string	$member 
	 * @return	DomainModel
	 */
	public function _markClean($member = null)
	{
		$this->_getDomainState()
			 ->markClean($member);

		return $this;
	}

	/**
	 * @param	string	$param
	 * @return	bool
	 */
	protected function isNonEmptyString($param)
	{
		return ! empty($param) && is_string($param);
	}
}
