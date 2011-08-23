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
namespace Example\Domain\User;

use Appfuel\Orm\Domain\DomainModel;

/**
 * The main purpose of this class is to test the combination of ObjectFactory
 * and DataBuilder to see if it can create and marshal this simple version
 * of a user
 */
class UserModel extends DomainModel
{
	protected $firstName = null;
	protected $lastName  = null;
	protected $email     = null;
}
