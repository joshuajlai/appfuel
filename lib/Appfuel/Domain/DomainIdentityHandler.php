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
namespace Appfuel\Domain;

use Appfuel\Orm\Identity\OrmIdentityHandler;

/**
 * Assigns the root level namespace and provides a place to make domain level
 * changes without having to change any orm code
 */
class DomainIdentityHandler extends OrmIdentityHandler
{

	/**
	 * Assign the root level namespace
	 * @return	IdentityHandler
	 */
	public function __construct()
	{
		$this->setRootNamespace(__NAMESPACE__);
	}
}
