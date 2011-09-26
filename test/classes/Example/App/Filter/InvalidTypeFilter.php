<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Example\App\Filter;

use AppFuel\App\Filter\AbstractFilter,
	Appfuel\Framework\App\Context\ContextInterface,
    Appfuel\Framework\App\Filter\InterceptingFilterInterface;

/**
 * Designed to have an invalid type which is neither pre or post
 */
class InvalidTypeFilter 
	extends AbstractFilter implements InterceptingFilterInterface
{
	/**
	 * @var string
	 */
	protected $type = 'foo';

	/** 
	 * override original constructor
	 * @return	InvalidTypeFilter
	 */
	public function __construct(){}

    public function filter(ContextInterface $context)
    {
        return $context;
    }
}
