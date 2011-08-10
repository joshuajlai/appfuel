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

use Appfuel\Orm\AbstractOrmFactory;

/**
 * The Role OrmFactory exposes custom source handler, identity handler 
 * but uses the orms databuilder, object factory and assembler
 */
class OrmFactory extends AbstractOrmFactory
{
	/**
	 * 
	 * @return	SourceHandler
	 */
	public function createSourceHandler()
	{

	}
}
