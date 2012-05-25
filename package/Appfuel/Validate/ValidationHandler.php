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
namespace Appfuel\Validate;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Validate\Filter\FilterInterface,
	Appfuel\Validate\Filter\FilterFactoryInterface;

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
			$coord = $this->createCoordinator();
		}
		$this->setCoordinator($coord);
	}

	/**
	 * @param	FieldSpecInterfaace $field	field used to filter on
	 * @return	ValidationHandler
	 */	
	public function addSingleFieldValidator(SingleFieldSpecInterface $spec)
	{	

		$field   = $spec->getField();
		$filters = $spec->getFilters();
		$class   = $spec->getValidator();	

		if (isset($this->validators[$field])) {
			$validator = $this->validators[$field];
			if ($validator instanceof ValidatorInterface) {
				$errmsg .= " validator must implment the ";
				$errmsg .= "Appfuel\Framework\Validate\ValidatorInterface";
				throw new RunTimeException($errmsg);
			}
				
			$validator->addFilter($fltr, $params, $err);
		}
		else {
			$validator = $this->createValidator($field, $fltr, $params, $err);
			$this->validators[$field] = $validator;
		}

		return $this;
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
	 * @return	array
	 */
	protected function getValidators()
	{
		return $this->validators;
	}

	/**
	 * Creates a validator with the first filter
	 * 
	 * @param	string	$field
	 * @param	FilterInterface	$filter
	 * @param	array	$params 
	 * @param	stirng	$error
	 * @return	Validator
	 */
	protected function createValidator($field, 
									   FilterInterface $filter, 
									   array $params = null,	
									   $error = null) 
									
	{
		return new Validator($field, $filter, $params, $error);
	}

	/**
	 * @param	FilterFactoryInterface $factory
	 * @return	null
	 */
	protected function setFilterFactory(FilterFactoryInterface $factory)
	{
		$this->filterFactory = $factory;
	}

	/**
	 * @return	Filter\FilterFactory
	 */
	protected function getFilterFactory()
	{
		return $this->filterFactory;	
	}

	/**
	 * @return	Filter\FilterFactory
	 */
	protected function createFilterFactory()
	{
		return new Filter\FilterFactory();
	}

	/**
	 * @param	CoordinatorInterface
	 * @return	null
	 */
	protected function setCoordinator(CoordinatorInterface $coord)
	{
		$this->coord = $coord;
	}

	/**
	 * Because the controller is a facade the coordinator is hidden for 
	 * internal implementation.
	 *
	 * @return	CoordinatorInterface
	 */
	protected function getCoordinator()
	{
		return $this->coord;	
	}

	/**	
	 * @return	Coordinator
	 */
	protected function createCoordinator()
	{
		return new Coordinator();
	}
}
