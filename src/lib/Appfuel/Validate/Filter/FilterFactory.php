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
namespace Appfuel\Validate\Filter;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Validate\Filter\FilterFactoryInterface;

/**
 * Create the filter from the name given. In this case the 
 */
class FilterFactory implements FilterFactoryInterface
{
	/**
	 * Decouple the filter name from the filter class name with a map
	 * @var array
	 */
	protected $map = array(
		'php-ip-filter'			=> 'PHPFilter\IpFilter',
		'php-float-filter'		=> 'PHPFilter\FloatFilter',
		'php-int-filter'		=> 'PHPFilter\IntFilter',
		'php-bool-filter'		=> 'PHPFilter\BoolFilter',
		'php-email-filter'		=> 'PHPFilter\EmailFilter',
		'php-regex-filter'		=> 'PHPFilter\RegexFilter',
		'php-url-filter'		=> 'PHPFilter\UrlFilter',
	);

	/**
	 * @param	string	$field		field used to filter on
	 * @return	FilterInterface
	 */	
	public function createFilter($name)
	{
		if (empty($name) || ! is_string($name) || 
				! array_key_exists($name, $this->map)) {
			return null;
		}

		$class = __NAMESPACE__ . "\\{$this->map[$name]}";
		return new $class($name);
	}
}
