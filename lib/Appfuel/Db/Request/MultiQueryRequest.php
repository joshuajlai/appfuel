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
namespace Appfuel\Db\Request;


use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Request\MultiQueryRequestInterface;


/**
 * Functionality needed to handle database request for queries
 */
class MultiQueryRequest 
	extends QueryRequest 
		implements MultiQueryRequestInterface
{
	/**
	 * Used to determine which adapter will service this request
	 * @var string
	 */
	protected $code = 'multiquery';

	/**
	 * Holds the options that map to each resultset. Options include:
	 *	resultKey - replaces the number index with resultKey
	 *	callback  - filters each row of the result with this callback
	 * @var array
	 */
	protected $resultOptions = array();
	
	/**
	 * @return	string
	 */
	public function getResultOptions()
	{
		return $this->resultOptions;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string	$type
	 * @return	RequestQuery
	 */
	public function setResultOptions(array $options)
	{
		$this->resultOptions = $options;
		return $this;
	}
}
