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
namespace Appfuel\Framework\Domain\InterceptFilter;

use Appfuel\Framework\Orm\Domain\DomainModelInterface;

/**
 * Filters are used by the front controller to apply business logic before
 * and after the action controller is executed. This interface is not the 
 * actual filter but the domain that describes the filter. The filter is 
 * created and assigned to the correct filter chain using this interface.
 */
interface InterceptFilterDomainInterface extends DomainModelInterface
{
	/**
	 * @return	string
	 */
	public function getKey();
	
	/**
	 * @param	string	$key
	 * @return	InterceptFilterDomainInterface
	 */
	public function setKey($key);

	/**
	 * @return	string
	 */
	public function getType();

	/**
	 * @param	string	$type	either pre|post
	 * @return	InterceptFilterDomainInterface
	 */
	public function setType($type);

	/**
	 * @return	string
	 */
	public function getDescription();

	/**
	 * @param	string
	 * @return	InterceptFilterDomainInterface
	 */
	public function setDescription($type);	
}
