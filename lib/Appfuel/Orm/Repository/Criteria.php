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
namespace Appfuel\Orm\Domain;

use BadMethodCallException,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Orm\Domain\DomainStateInterface,
	Appfuel\Framework\Orm\Domain\DomainModelInterface,
	Appfuel\Framework\Orm\Repository\DbRespositoryInterface;

/**
 * The repository is facade for the orm systems. Developers use the repo to 
 * create, modify, delete or find domains in the database. 
 */
abstract class DbRepository implements DbRepositoryInterface
{
	
}
