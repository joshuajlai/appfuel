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
namespace Appfuel\Validate;

use InvalidArgumentException,
	Appfuel\DataStructure\Dictionary,
	Appfuel\Validate\Filter\FilterInterface,
	Appfuel\Validate\FieldValidatorInterface;

/**
 * During validation the validator grab the field from the coordinators raw
 * source, runs it through a list of filters and sanitizers and either reports
 * errors back to the coordinator or adds the now clean data into the 
 * coordinators clean datasource.
 */
class Validator implements FieldValidatorInterface
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
	 * @param	CoordinatorInterface
	 * @return	Controller
	 */
	public function __construct($field, 
								FilterInterface $filter = null,
								array $params = null, 
								$error = null)
	{
		if (empty($field) || ! is_scalar($field)) {
			throw new InvalidArgumentException(
				"Field must be a non empty scalar"
			);
		}

		$this->field = $field;
		if (null !== $filter) {
			$this->addFilter($filter, $params, $error);
		}
	}

	/**
	 * @return	string
	 */
	public function getField()
	{
		return $this->field;
	}

	/**
	 * @param	FilterInterface	$filter		name of the filter need for field
	 * @param	array			$params		optional parameters
	 * @param	string			$error		text used when filter fails
	 * @return	Validate
	 */	
	public function addFilter(FilterInterface $filter, 
							  array $params = null,
							 $error = null)
	{
		$this->filters[] = array(
			'filter' => $filter, 
			'params' => $params,
			'error'  => $error
		);

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

			/* all filters require a dictionary even if empty */
			$params = new Dictionary($params);

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
}
