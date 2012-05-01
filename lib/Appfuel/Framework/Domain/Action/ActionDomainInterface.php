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
namespace Appfuel\Framework\Domain\Action;

use Appfuel\Framework\Orm\Domain\DomainModelInterface;

/**
 * Used by the front controller, the action domain holds the namespace of the
 * action controller.
 */
interface ActionDomainInterface extends DomainModelInterface
{
	/**
	 * @return	string
	 */
	public function getNamespace();
	
	/**
	 * @param	string	$path
	 * @return	ActionDomainInterface
	 */
	public function setNamespace($path);
}
