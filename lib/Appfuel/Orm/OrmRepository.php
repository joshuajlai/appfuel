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
namespace Appfuel\Orm;

use Appfuel\Orm\OrmFactoryInterface,
	Appfuel\DataStructure\Dictionary;

/**
 * The repository is facade for the orm systems. Developers use the repo to 
 * create, modify, delete or find domains in the database. 
 */
class OrmRepository implements OrmRepositoryInterface
{
	/**
	 * Criteria stores options in the form of key/value pair and named 
	 * expression lists which are generally used by the data source to
	 * construct things like sql.
	 *
	 * @var Criteria
	 */
	protected $criteria = null;

	/**
	 * @var OrmDataSourceInterface
	 */
	protected $dataSource = null;

	/**
	 * @param	OrmFactoryInterface $factory
	 * @return	OrmRepository
	 */
	public function __construct($source)
	{
		$this->dataSource = $source;
	}

	/**
	 * @return mixed
	 */
	public function getDataSource()
	{
		return $this->dataSource;
	}

	/**
	 * @param	array	$data
	 * @return	Dictionary
	 */
	public function createDictionary(array $data = null)
	{
		return new Dictionary($data);
	}
}
