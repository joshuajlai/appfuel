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

use Appfuel\Orm\Domain\DomainModel;

/**
 * The role model is part of the role based access control. It represents the
 * job function or title which defines the authority level
 */
class RoleModel extends DomainModel
{
	/**
	 * Full name of the role as used to label the role in the user interface
	 * @var string
	 */
	protected $name = null;

	/**
	 * Authority level is a code which represents the job function (auth level)
	 * for this role
	 * @var string
	 */
	protected $authLevel = null;
	
	/**
	 * Short description of the reposibility this role governs
	 * @var string
	 */
	protected $description = null;
}
