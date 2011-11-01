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
namespace TestFuel\Fake\Domain\User;

use Appfuel\Orm\Domain\DomainModel;

/**
 * The main purpose of this class is to test the combination of ObjectFactory
 * and DataBuilder to see if it can create and marshal this simple version
 * of a user
 */
class UserDomain extends DomainModel
{
	protected $firstName = null;
	protected $lastName  = null;
	protected $email     = null;

	public function setEmail(Email\EmailDomain $email)
	{
		$this->email = $email;
		$this->_markDirty('email');
		return $this;
	}

	public function _marshal(array $data = null)
	{
        $state = $this->_getDomainState();
        $state->markMarshal($data);

        if (empty($data)) {
            return $this;
        }
		
		if (! isset($data['firstName'])) {
			throw new Exception("first name must be set");
		}
		$this->setFirstName($data['firstName']);

		if (! isset($data['lastName'])) {
			throw new Exception("last name must be set");
		}
		$this->setLastName($data['lastName']);

		if (! isset($data['email'])) {
			throw new Exception("email must be set");
		}
		$email = new Email\EmailDomain();
		$email->_marshal($data['email']);
		$this->setEmail($email);

		$this->_markClean();
	}
}
