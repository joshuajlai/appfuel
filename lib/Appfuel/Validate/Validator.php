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

use Appfuel\Framework\Exception,
	Appfuel\Framework\Validate\CoordinatorInterface,
	Appfuel\Framework\Validate\Filter\FilterInterface,
	Appfuel\Framework\Validate\SingleFieldValidatorInterface;,

/**
 * During validation the validator grab the field from the coordinators raw
 * source, runs it through a list of filters and sanitizers and either reports
 * errors back to the coordinator or adds the now clean data into the 
 * coordinators clean datasource.
 */
class Validator implements SingleFieldValidatorInterface
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
								$errorMessage = null)
	{
		
	}

	/**
	 * @param	string	$field		field used to filter on
	 * @param	string	$error		text used when filter fails
	 * @param	string	$filter		name of the filter need for field
	 * @param	array	$params		list of optional parameter for filter
	 * @return	Controller
	 */	
	public function addFilter($field, $error, $filter, array $params = null)
	{

	}

	/**
	 * @param	mixed	$raw	data used to validate with filters
	 * @return	bool
	 */
	public function validate($raw)
	{

	}

	/**
	 * @return array
	 */
	public function getErrors()
	{

	}

	/**
	 * @return	array
	 */
	public function getAllClean()
	{

	}

	/**
	 * @return	mixed
	 */
	public function getClean($field)
	{

	}

	/**
	 * @return	bool
	 */
	public function isError()
	{

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
}
