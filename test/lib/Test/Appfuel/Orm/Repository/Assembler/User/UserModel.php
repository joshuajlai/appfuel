<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Test\Appfuel\Orm\Repository\Assembler\User;

use Appfuel\Orm\Domain\DomainModel;

/**
 * Fake user model used to prove the assembler can build a domain described
 * by a criteria
 */
class UserModel extends DomainModel
{
	protected $firstName = null;
	protected $lastName  = null;
	protected $email     = null;
}
