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
	 * @param	SingleFieldSpecInterface $spec
	 * @return	SingleFieldValidator
	 */
	public function loadSpec(SingleFieldSpecInterface $spec)
	{
		$this->setField($spec->getField());
		
	}

	/**
	 * Run though each filter and pass the raw data into it, reporting back 
	 * any errors to the coordinator. We do not bail when a failure is first
	 * detected. Instead we not the error occurs and continue to feed the 
	 * raw data into the next filter until all filters have run. When no errors
	 * have occured we add the clean data into the coordinator and return true
	 * otherwise we return false
	 *
	 * @param	mixed	$raw	data used to validate with filters
	 * @return	bool
	 */
	public function isValid(CoordinatorInterface $coord)
	{
		$field = $this->getField();
		$raw   = $coord->getRaw($field);
		
		$isError   = false;
		$filters   = $this->getFilters();
		foreach ($filters as $list) {
			if (! is_array($list) || ! isset($list['filter'])) {
				continue;
			}
			
			$filter = $list['filter'];

			$params = array();
			if (isset($list['params']) && is_array($list['params'])) {
				$params = $list['params'];
			}

			$error  = $filter->getDefaultError();
			if (isset($list['error']) && is_string($list['error'])) {
				$error = $list['error'];
			}

			$clean = $filter->filter($raw, $params);
			if ($filter->isFailure()) {
				$coord->addError($field, $error);
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
		$this->filters = array();
		$this->error   = null;
	}
}
