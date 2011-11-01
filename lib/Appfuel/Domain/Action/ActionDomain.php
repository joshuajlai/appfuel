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
 * The primary responsibility is to hold the namespace of the action controller
 */
class ActionDomain extends DomainModel implements ActionDomainInterface
{
	/**
	 * This is the rootNamespace for this action controller. The 
	 * @var string
	 */
	protected $namespace = null;

	/**
	 * @param	string	$path
	 * @return	OperationModel
	 */
	public function setNamespace($path)
	{
		if (! is_string($path)) {
			throw new Exception("namespace must be a non empty string");
		}
		
		$this->namespace = $path;
		$this->_markDirty('namespace');
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}
}
