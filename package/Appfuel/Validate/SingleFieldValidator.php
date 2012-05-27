<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Validate;

use InvalidArgumentException,
	Appfuel\Validate\Filter\FilterInterface,
	Appfuel\Validate\FieldValidatorInterface;

/**
 */
class SingleFieldValidator implements SingleFieldValidatorInterface
{
	/**
	 * Name of the field in the source we are looking to validate. The source
	 * could be any super global or user created array of data
	 * @var string
	 */
	protected $field = null;

	/**
	 * List of filters and sanitizers to run against the field's value
	 * @var array
	 */
	protected $filters = array();

	/**
	 * @var string
	 */
	protected $error = null;

	/**
	 * @return	string
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * @param	string	$name
	 * @return	SingleFieldValidator
	 */
	public function setField($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = "field must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->field = $name;
		return $this;
	}

	/**
	 * @return	SingleFieldValidator
	 */
	public function clearField()
	{
		$this->field = null;
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getFilters()
	{
		return $this->filters;
	}

	/**
	 * @param	FilterInterface $filter
	 * @return	SingleFieldValidator
	 */
	public function addFilter(FilterInterface $filter)
	{
		$this->filters[] = $filter;
		return $this;
	}

	/**
	 * @return	SingleFieldValidator
	 */
	public function clearFilters()
	{
		$this->filters = array();
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @param	string	$text
	 * @return	SingleFieldValidator
	 */
	public function setError($text)
	{
		if (! is_string($text)) {
			$err = "error text must be a string";
			throw new InvalidArgumentException($err);
		}

		$this->error = $text;
		return $this;
	}

	/**
	 * @return	SingleFieldValidator
	 */
	public function clearError()
	{
		$this->error = null;
		return $this;
	}

	/**
	 * @param	SingleFieldSpecInterface $spec
	 * @return	SingleFieldValidator
	 */
	public function loadSpec(SingleFieldSpecInterface $spec)
	{
		$this->setField($spec->getField());
		
		$filters = $spec->getFilters();
		foreach ($filters as $filterSpec) {
			$filter = ValidationManager::getFilter($filterSpec->getName());
			$filter->loadSpec($filterSpec);
			$this->addFilter($filter);
		}

		$error = $spec->getError();
		if (! empty($error)) {
			$this->setError($error);
		}

		return $this;
	}

	/**
	 * Run though each filter and pass the raw data into it, reporting back 
	 * any errors to the coordinator. We do not bail when a failure is first
	 * detected. Instead we continue to feed the raw data into the next filter 
	 * until all filters have run. When no errors have occured we add the 
	 * clean data into the coordinator
	 *
	 * @param	mixed	$raw	data used to validate with filters
	 * @return	bool
	 */
	public function isValid(CoordinatorInterface $coord)
	{
		$field = $this->getField();
		$raw = $coord->getRaw($this->getField());
		if (CoordinatorInterface::FIELD_NOT_FOUND === $raw) {
			return false;
		}
		
		$isError = false;
		$filters = $this->getFilters();
		$error   = $this->getError();
		$error   = (! empty($error)) ? "$error " : '';
		foreach ($filters as $filter) {
			$clean = $filter->filter($raw, $params);
			if (FilterInterface::FILTER_FAILURE === $clean) {
				$coord->addError("$error{$filter->getError()}");
				$isError = true;
				continue;
			}
			
			/* 
			 * the newly clean data becomes the raw data for the next filter
			 */
			$raw = $clean;
		}

		if ($isError) {
			return false;
		}

		$coord->addClean($field, $clean);
		return true;
	}

	/**
	 * @return	null
	 */
	public function clear()
	{
		$this->clearField();
		$this->clearFilters();
		$this->clearError();
	}
}
