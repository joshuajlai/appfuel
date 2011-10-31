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
namespace Appfuel\Framework\Orm\Domain;

/**
 * Used to turn domain keys into domain domain objects. Its not the factory's
 * responsibility to build full domain objects marshalled and ready to go, 
 * only to map the domain key to a fully qualified class name and instantiate 
 * it
 */
interface DomainObjectFactoryInterface
{
	/**
	 * @param	string	$key	used to determine which object to create
	 * @return	mixed
	 */
	public function createDomainObject($key);
}
