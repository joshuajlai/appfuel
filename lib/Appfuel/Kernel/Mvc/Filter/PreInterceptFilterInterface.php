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


use Appfuel\Kernel\Mvc\MvcContextInterface,
	Appfuel\Kernel\Mvc\ContextBuilderInterface;

/**
 */
interface PreInterceptFilterInterface extends InterceptFilterInterface
{
	public function getContextBuilder();
	public function setContextBuilder(ContextBuilderInterface $builder);
}
