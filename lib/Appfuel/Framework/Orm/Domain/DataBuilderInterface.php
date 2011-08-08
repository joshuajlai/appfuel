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
 * Data Builder turns already mapped data into domain objects, strings, 
 * json or any other format required.
 */
interface DataBuilderInterface
{
	/**
	 * @param	string	$key	used to determine which object to create
	 * @return	mixed
	 */
	public function getObjectFactory();

	/**
	 * @param	string	$key
	 * @param	array	$data  
	 * @return	DomainModel
	 */
	public function buildDomainModel($key, array $data);
}
