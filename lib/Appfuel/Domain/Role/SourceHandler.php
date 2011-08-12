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
	public function fetchDesendantsById(CriteriaInterface $criteria)
	{
		$path = 'Sql/templates/selectDescendantsOf.psql';
		$template = $this->createTemplate($path);
		
		$id = $criteria->get('id', 0);
		$sql = $template->build(array('role_id' => $id));
	
		$request = $this->createRequest('query', 'read');
		$request->setSql($sql);

		$response = $this->sendRequest($request);
		if ($response->isError()) {
			return $response->getError();
		}
	
		return $response->getResultset();
	}
}
