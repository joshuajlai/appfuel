<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuele@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\App;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Validate\CoordinatorInterface,
	Appfuel\Validate\ValidationHandlerInterface,

/**
 * This is a Facade that unifies all the validation subsystems allowing
 * a set of one or more rules to be applied to a single field and have errors
 * associated to that field.
 */
class ValidationHandler implements ValidationHandlerInterface
{
	/**
	 * Used to handle the movement of data and errors between the 
	 * validation subsystems
	 * @var Coordinator
	 */
	protected $coord = null;

	/**
	 * Holds a list of validators based on field name
	 * @var array
	 */
	protected $validators = array();

	/**
	 * @param	CoordinatorInterface
	 * @return	Controller
	 */
	public function __construct(CoordinatorInterface $coord = null)
	{
		if (null === $coord) {
			$coord = ValidationManager::createCoordinator();
		}
		$this->setCoordinator($coord);
	}

	/**
	 * @param	CoordinatorInterface
	 * @return	null
	 */
	public function setCoordinator(CoordinatorInterface $coord)
	{
		$this->coord = $coord;
	}

	/**
	 * @return	CoordinatorInterface
	 */
	public function getCoordinator()
	{
		return $this->coord;	
	}

	/**
	 * @return	array
	 */
	public function getValidators()
	{
		return $this->validators;
	}

	/**
	 * @return	bool
	 */
	public function isError()
	{
		return $this->getCoordinator()
					->isError();
	}

	/**
	 * @return array
	 */
	public function getErrors()
	{
		return $this->getCoordinator()
					->getErrors();
	}

	/**
	 * @return	Error | null when no errors exist
	 */
	public function getError($field)
	{
		return $this->getCoordinator()
					->getError($field);
	}

	/**
	 * @return	array
	 */
	public function getAllClean()
	{
		return $this->getCoordinator()
					->getAllClean();
	}

	/**
	 * @return	mixed
	 */
	public function getClean($field, $default = null)
	{
		return $this->getCoordinator()
					->getClean($field, $default);
	}


	/**
	 * @param	mixed	$raw	data used to validate with filters
	 * @return	bool
	 */
	public function isSatisfiedBy(array $raw)
	{
		$coord = $this->getCoordinator();
		
		/* 
		 * Clear any errors, clean and raw data. this allows filters to be
		 * reused across multiple raw sources
		 */
		$coord->reset();
		$coord->setSource($raw);
		
		$failureCount  = 0;
		$validators = $this->getValidators();
		foreach ($validators as $field => $validator) {
			if (! $validator->isValid($coord)) {
				$failureCount++;
			}
		}

		if ($failureCount > 0) {
			return false;
		}

		return true;
	}
}
