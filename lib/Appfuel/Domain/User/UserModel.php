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
namespace Appfuel\Domain\User;

use Appfuel\Orm\Domain\DomainModel;

/**
 * Appfuel User domain model
 */
class UserModel extends DomainModel
{
	/**
	 * Name used to login into the system
	 * @var string
	 */
	protected $loginName = null;

	/**
	 * @var string
	 */
	protected $firstName = null;
	
	/**
	 * @var string
	 */
	protected $lastName = null;
	
	/**
	 * User primary email
	 * @var string
	 */
	protected $email = null;
	
	/**
	 * Used to determine the status of the user in the system values range
	 * form 'active', 'inactive', 'suspended', 'removed'
	 * @var string
	 */
	protected $activityCode = null;
	
	/**
	 * Date the user account was created
	 * @var string
	 */
	protected $dateCreated = null;
	
	/**
	 * Last time the user accessed the system
	 * @var string
	 */
	protected $lastAccessed = null;	
}
