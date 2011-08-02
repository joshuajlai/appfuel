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
	Appfuel\Framework\Validate\ControllerInterface,
	Appfuel\Framework\Validate\CoordinatorInterface;

/**
 * This is a Facade that unifies all the validation subsystems allowing
 * a set of one or more rules to be applied to a single field and have errors
 * associated to that field.
 */
class Controller implements ControllerInterface
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
		if (null !== $coord) {
			$coord = new Coordinator();
		}

		$this->coord = $coord;
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
