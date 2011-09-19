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
namespace Appfuel\App\Filter;

use Appfuel\Framework\Exception,
	Appfuel\Framework\App\ContextInterface,
	Appfuel\Framework\App\Filter\InterceptingFilterInterface;

/**
 * Since everyone belongs to an organization this filter
 */
class OrgFilter extends InterceptingFilter
{
	/**
	 * Determines is this is a post or pre filter
	 * @var	string
	 */
	protected $type = null;
	
	/**
	 * @return	AuthFilter
	 */
	public function __construct()
	{
		$this->type = 'pre';
	}

	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return	bool
	 */
	public function isError()
	{
	}

	public function getError()
	{

	}

	public function filter(ContextInterface $context)
	{
		echo "\n", print_r('i am the auth filter',1), "\n";exit;
	}
}	
