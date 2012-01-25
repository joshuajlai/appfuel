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
namespace Appfuel\Kernel\Mvc\Filter;

use InvalidArgumentException,
	Appfuel\Kernel\Mvc\MvcContextInterface,
	Appfuel\Kernel\Mvc\ContextBuilder,
	Appfuel\Kernel\Mvc\ContextBuilderInterface;

/**
 * Any filter extending this is guaranteed to be a pre filter
 */
class PreInterceptFilter 
	extends GeneralIntercept implements PreInterceptFilterInterface
{

	/**
	 * @param	string	$type	pre|post
	 * @param	InterceptingFilterInterface	$next
	 */
	public function __construct(ContextBuilderInterface $builder = null)
	{
		if (null === $builder) {
			$builder = new ContextBuilder();
		}
		$this->setContextBuilder($builder);
	}

    /**
     * This should be extended. 
     *
     * @param   MvcContextInterface
     * @return  MvcContextInterface | null 
     */
    public function filter(MvcContextInterface $context)
    {
        return $this->next($context);
    }

    /**
     * @return  ContextBuilderInterface
     */
    public function getContextBuilder()
    {
        return $this->builder;
    }

    /**
     * @param   ContextBuilderInterface $builder
     * @return  null
     */
    public function setContextBuilder(ContextBuilderInterface $builder)
    {
        $this->builder = $builder;
    }
}
