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
namespace Appfuel\Domain\Operation;

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
		parent::__construct('operation');
	}

	/**
	 * @return	null
	 */
	public function loadMaps()
	{
		$columns = array(
			'id'			=> 'op_id',
			'name'			=> 'op_name',
			'description'	=> 'op_desc'
		);

		$map = new ArrayMap($columns);
		$this->addMapper('memberToColumn', $map->getKeyToValueMapper());
		$this->addMapper('columnToMember', $map->getValueToKeyMapper());

	}
}
