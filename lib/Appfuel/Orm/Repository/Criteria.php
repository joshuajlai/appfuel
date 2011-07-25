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
namespace Appfuel\Orm\Repository;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Orm\Repository\CriteriaInterface;

/**
 * The criteria holds any information necessary for the sql factory to build
 * the correct sql for the db request to pull domain information down from 
 * the database
 */
class Criteria implements CriteriaInterface
{
	/**
	 * Name of the primary domain used in the sql statement. This is given
	 * as the domain-key mapped in the domain identity and not the domain
	 * class name.
	 * @var string
	 */
	protected $targetDomain = null;

	/**
	 * Name of the type of operation this criteria repersents
	 * @var string
	 */
	protected $opType = null;

	/**
	 * Filters are a list of expressions that can be separated by one of 
	 * to logical operators AND|OR the last expression has no operator
	 * @var array
	 */
	protected $filters = array();

	/**
	 * @return string
	 */
	public function getTargetDomain()
	{
		return $this->targetDomain;
	}

	/**
	 * @param	string	$domainKey
	 * @return	Criteria
	 */
	public function setTargetDomain($domainKey)
	{
		if (! $this->isValidString($domainKey)) {
			throw new Exception("domainKey must be a non empty string");
		}

		$this->targetDomain = $domainKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getOperationType()
	{
		return $this->opType;
	}

	/**
	 * @param	string	$domainKey
	 * @return	Criteria
	 */
	public function setOperationType($type)
	{
		if (! $this->isValidString($type)) {
			throw new Exception("operation must be a non empty string");
		}

		$this->opType = $type;
		return $this;
	}


	/**
	 * @param	mixed	$str
	 * @return	bool
	 */
	protected function isValidString($str)
	{
		if (empty($str) || ! is_string($str)) {
			return false;
		}

		return true;
	}
}
