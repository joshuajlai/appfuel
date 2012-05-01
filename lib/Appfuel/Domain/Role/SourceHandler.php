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

use Appfuel\Orm\Source\Db\OrmSourceHandler,
	Appfuel\Framework\Orm\Repository\CriteriaInterface;

/**
 * Used to perform specific database operations and provide a mapped dataset.
 */
class SourceHandler extends OrmSourceHandler
{
	/**
	 * Fetch a list of desendants based on the role id
	 *
	 * @param	CriteriaInterface $criteria
	 * @return	array | DbErrorInterface on failure
	 */
	public function fetchSubTreeById(CriteriaInterface $criteria)
	{
		$path = 'Sql/templates/selectDescendantsOf.psql';
		$template = $this->createTemplate($path);
		
		$valid = array('ancestor', 'descendant');
		$type  = $criteria->get('closure-type', $valid[1]);
		/*
		 * @todo generate error object here
		 */
		if (! in_array($type, $valid)) {
			return false;
		}
		$template->assign('closure-type', $type);
		$template->assign('node-id', $criteria->get('node-id', 0));
	
		$request = $this->createRequest('query', 'read');
		$request->setSql($template->build());

		$response = $this->sendRequest($request);
		if ($response->isError()) {
			return $response->getError();
		}
	
		return $response->getResultset();

	}

	/**
	 * @param	CriteriaInterface $criteria
	 * @return	bool
	 */
	public function insertSubtree(CriteriaInterface $criteria)
	{
		//$path = 'Sql/templates/insertIntoRoleTree.psql';
		//$template = $this->createTemplate($path);
		
		$sql  = "INSERT INTO test_queries(param_1, param_2, param_3, result) ";
		$sql .= "VALUES (1, 'a', 1, 'a')";

		$sql1 = "SELECT * FROM test_queries";

		$sql2 = "SELECT * FROM test_queries";
		$request = $this->createRequest('multiquery', 'write');
		$request->addSql($sql1)
				->addSql($sql2);

		$response = $this->sendRequest($request);
		echo "\n", print_r($response,1), "\n";exit;
		if ($response->isError()) {
			return $response->getError();
		}
	
		return $response->getResultset();
	}
}
