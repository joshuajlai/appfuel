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

use Appfuel\Orm\Source\Db\OrmSourceHandler,
	Appfuel\Framework\Orm\Repository\CriteriaInterface;

/**
 * Used to perform specific database operations and provide a mapped dataset.
 */
class SourceHandler extends OrmSourceHandler
{
	/**
	 * Fetch qll operations for the application
	 *
	 * @param	CriteriaInterface $criteria
	 * @return	array | DbErrorInterface on failure
	 */
	public function fetchAllOperations(CriteriaInterface $criteria)
	{
		$path = 'Sql/templates/some_tempate.psql';
		$template = $this->createTemplate($path);
		
		$template->assign('assign-something', $criteria->get('some-id', 0));
	
		$request = $this->createRequest('query', 'read');
		$request->setSql($template->build());

		$response = $this->sendRequest($request);
		if ($response->isError()) {
			return $response->getError();
		}
	
		return $response->getResultset();
	}
}
