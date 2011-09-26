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
namespace Appfuel\Domain\Action;

use Appfuel\Framework\Exception,
	Appfuel\Orm\Domain\DomainModel,
	Appfuel\Framework\Domain\Action\ActionDomainInterface;

/**
 * The action domain describes the controller action which executes a given
 * request. This class is not the controller but holds information necessary
 * to create an action controller
 */
class ActionModel extends DomainModel implements ActionDomainInterface
{
	/**
	 * This is the rootNamespace for this action controller. The 
	 * @var string
	 */
	protected $rootNamespace = null;

	/**
	 * This is the relative namespace of this action controller. 
	 * @var string
	 */
	protected $relativeNamespace = null;

	/**
	 * The absolute namespace to the action controller
	 * @var	null
	 */
	protected $actionNamespace = null;

	/**
	 * Classname of the action controller
	 * @var string
	 */
	protected $controllerClass = null;

	/**
	 * Name of the class used to build the action controller
	 * @var string
	 */
	protected $builderClass = null;
	
	/**
	 * @param	string
	 */
	public function getRootNamespace()
	{
		return $this->rootNamespace;
	}

	/**
	 * @param	string	$ns
	 * @return	OperationModel
	 */
	public function setRootNamesapce($ns)
	{
		if (! is_string($ns)) {
			throw new Exception("Operation name must be a non empty string");
		}
		
		$this->rootNamespace = $name;
		$this->_markDirty('rootNamespace');
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getRelativeNamespace()
	{
		return $this->relativeNamespace;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string	$class	either business|infra|ui
	 * @return	OperationModel
	 */
	public function setRelativeNamespace($ns)
	{
		if (! $this->isNonEmptyString($ns)) {
			throw new Exception("Operation class must be a non empty string");
		}

		$this->relativeNamespace = $ns;
		$this->_markDirty('relativeNamespace');
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getActionNamespace()
	{
		return "{$this->rootNamespace}\\{$this->relativeNamespace}";
	}

	/**
	 * @param	string
	 */
	public function getControllerClass()
	{
		return $this->controllerClass;
	}

	/**
	 * @param	string	$ns
	 * @return	OperationModel
	 */
	public function setControllerClass($class)
	{
		if (! $this->isNonEmptyString($ns)) {
			throw new Exception("Operation name must be a non empty string");
		}
		
		$this->controllerClass = $name;
		$this->_markDirty('controllerClass');
		return $this;
	}

	/**
	 * @param	string
	 */
	public function getBuilderClass()
	{
		return $this->builderClass;
	}

	/**
	 * @param	string	$ns
	 * @return	OperationModel
	 */
	public function setBuilderClass($class)
	{
		if (! $this->isNonEmptyString($class)) {
			throw new Exception("Operation name must be a non empty string");
		}
		
		$this->builderClass = $class;
		$this->_markDirty('builderClass');
		return $this;
	}
}
