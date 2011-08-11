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
namespace Appfuel\Domain\Role;

use Appfuel\Domain\DomainIdentityHandler;

/**
 * Used to manage database interaction throught a uniform interface that 
 * does not care about the specifics of the database
 */
class IdentityHandler extends DomainIdentityHandler
{

	public function __construct()
	{
		$this->setRootNamespace('Appfuel\Domain');
		$this->setDomainName('role');
	}
}
