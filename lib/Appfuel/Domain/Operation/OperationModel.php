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
namespace Appfuel\Domain\Operation;

use Appfuel\Framework\Exception,
	Appfuel\Orm\Domain\DomainModel,
	Appfuel\Framework\Action\ActionControllerDetail,
	Appfuel\Framework\Domain\Operation\OperationDomainInterface;

/**
 * An operation represents an action that can be preformed by a user or system.
 * The framework identitifies an operation by its route string while users 
 * refer to it by its name. The route string is a public string used to map an 
 * operation to an action controller namespace, decoupling the action 
 * controller class from the url used to request that action controller. 
 */
class OperationModel extends DomainModel implements OperationDomainInterface
{
	/**
	 * Textual name 
	 * @var string
	 */
	protected $name = null;
	
	/**
	 * The class of operation the operation belongs to. Current their are the 
	 * following classes: business|infrastructure|ui
	 *
	 * @var string
	 */
	protected $opClass = null;

	/**
	 * @var string
	 */
	protected $description = null;
	
	/**
	 * @param	string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param	string	$name
	 * @return	OperationModel
	 */
	public function setName($name)
	{
		if (! $this->isNonEmptyString($name)) {
			throw new Exception("Operation name must be a non empty string");
		}
		
		$this->name = $name;
		$this->_markDirty('name');
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getOpClass()
	{
		return $this->opClass;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string	$class	either business|infra|ui
	 * @return	OperationModel
	 */
	public function setOpClass($class)
	{
		if (! $this->isNonEmptyString($class)) {
			throw new Exception("Operation class must be a non empty string");
		}

		$class = strtolower($class);
		if (! in_array($class, array('business','infra', 'ui'))) {
			$err = "Operation class must be business|infra|ui -($class)";
			throw new Exception($err);
		}

		$this->opClass = $class;
		$this->_markDirty('opClass');
		return $this;
	}


	/**
	 * @return	string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param	string	$text
	 * @return	OperationModel
	 */
	public function setDescription($text)
	{
		if (! is_string($text)) {
			throw new Exception("Invalid description must be a string");
		}

		$this->description = $text;
		$this->_markDirty('description');
		return $this;
	}

}
