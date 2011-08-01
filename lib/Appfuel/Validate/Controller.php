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
	 * @param	CoordinatorInterface
	 * @return	Controller
	 */
	public function __construct(CoordinatorInterface $coord = null)
	{
		if (null === $coord) {
			$coord = new Coordinator();
		}
		$this->setCoordinator($coord);
	}

	/**
	 * @return	CoordinatorInterface
	 */
	public function getCoordinator()
	{
		return $this->coord;	
	}

	/**
	 * @param CoordinatorInterface
	 * @return	Controller
	 */
	public function setCoordinator(CoordinatorInterface $coord)
	{
		$this->coord = $coord;
		return $this;
	}
}
