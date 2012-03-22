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
	public function __construct($source, OrmCriteriaInterface $crit = null)
	{
		$this->dataSource = $source;
		if (null === $crit) {
			$crit = $this->createCriteria();
		}
		$this->setCriteria($crit);
	}

	/**
	 * @return mixed
	 */
	public function getDataSource()
	{
		return $this->dataSource;
	}

	/**
	 * @return	OrmCriteriaInterface
	 */
	public function getCriteria()
	{
		return $this->criteria;
	}

	/**
	 * @param	OrmCriteriaInterface $criteria
	 * @return	OrmRepository
	 */
	public function setCriteria(OrmCriteriaInterface $criteria)
	{
		$this->criteria = $criteria;
		return $this;
	}

	/**
	 * @param	array	$data
	 * @return	OrmSearchDetail
	 */
	public function createSearchDetail(array $data)
	{
		return new OrmSearchDetail($data);
	}

	/**
	 * @param	array	$list	
	 * @param	array	$param
	 * @return	OrmCriteria
	 */
	public function createCriteria(array $list = null, array $params = null)
	{
		return new OrmCriteria($list, $params);
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
