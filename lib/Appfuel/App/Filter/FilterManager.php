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
	Appfuel\Framework\App\Filter\FilterManagerInterface,
	Appfuel\Framework\App\Filter\InterceptingFilterInterface;

/**
 * The filter manager is a facade which handles the usage of intercepting
 * filters. Unlike the traditional intercepting filter that forms a chain 
 * where each filter has the next filter. We hold the filters in two simple
 * arrays. 
 * 
 *	Pre		holds all the filters to be loaded before the action controller
 *			is executed. 
 *	Post	holds all the filters to be loaded after the action controller
 *			has executed but before the content is outputted.
 */
class FilterManager implements FilterManagerInterface
{
	/**
	 * List of pre filters
	 * @var array
	 */
	protected $pre = array();

	/**
	 * List of post filters
	 * @var array
	 */
	protected $post = array();


	public function loadFilters($filters)
	{
		if (empty($filters)) {
			return $this;
		}

		if (is_string($filters)) {
			$filters = array($filters);
		}
		else if (! is_array($filters)) {
			throw new Exception("param given must be a string or array");
		}

		$filters = array_reverse($filters);
		$target  = null;
		foreach ($filters as $index => $class) {
			if (empty($class) || ! is_string($class)) {
				throw new Exception("Invalid filter given at index -($index)");
			}
			$filter = new $class();
			if (! $filter instanceof InterceptingFilterInterface) {
				throw new Exception("Filter does not implment interface");
			}

			$type = $filter->getType();
			$this->addFilter($type, $filter);
		}
	}
}	
