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

use Appfuel\Domain\DomainIdentityHandler,
	Appfuel\Framework\DataStructure\ArrayMap;

/**
 * Used to manage database interaction throught a uniform interface that 
 * does not care about the specifics of the database
 */
class IdentityHandler extends DomainIdentityHandler
{
	/**
	 * Define the domain name this identity is in charge of
	 * 
	 * @return	IdentityHandler
	 */
	public function __construct()
	{
		parent::__construct('role');
	}

	/**
	 * @return	null
	 */
	public function loadMaps()
	{
		$columns = array(
			'id'			=> 'role_id',
			'name'			=> 'role_name',
			'authLevel'		=> 'role_code',
			'description'	=> 'role_desc'
		);

		$map = new ArrayMap($columns);
		$this->addMapper('memberToColumn', $map->getKeyToValueMapper());
		$this->addMapper('columnToMember', $map->getValueToKeyMapper());

		$tables = array(
			'role'		=> 'roles',
			'closure'	=> 'role_paths'
		);
		$map = new ArrayMap($tables);
		$this->addMapper('table', $map->getKeyToValueMapper());
	}
}
