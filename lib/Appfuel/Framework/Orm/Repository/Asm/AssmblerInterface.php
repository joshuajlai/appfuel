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
namespace Appfuel\Framework\Orm\Repository\Asm;

use Appfuel\Framework\Orm\Repository\CriteriaInterface;

/**
 * The assembler is reposible for processing the repo's criteria into a 
 * set of details that can be used to execute a request against a datasource
 * and any errrors and process the results into domain data also based on
 * on the criteria given by the repository
 */
interface AssemblerInterface
{
	/**
	 * Turn criteria into a set of known details to be used by the data source
	 *
	 * @param	CriteriaInterface	$criteria
	 * @return	mixed
	 */
	public function processCriteria(CriteriaInterface $criteria);

	/**
	 * Take a set of details and use them to execute a request against some
	 * datasource producing a known reponse to be handled by processResults
	 *
	 * @param	mixed	$details
	 * @return	mixed
	 */
	public function executeDataSource($details);
	
	/**
	 * Take the datasource response and turn it into a known set of domains
	 * or domain mapped datasets like arrays, strings etc... based on
	 * the repos's criteria
	 *
	 * @param	mixed	$data
	 * @param	mixed	$criteria	optional
	 * @return	mixed
	 */
	public function processResults($data, CriteriaInterface $criteria = null);
}
