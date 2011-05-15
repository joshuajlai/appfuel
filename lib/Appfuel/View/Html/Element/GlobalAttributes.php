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
namespace Appfuel\View\Html\Element;

use Appfuel\Framework\Exception;

/**
 * 
 */
class GlobalAttributes
{
	/**
	 * White list of accepted attributes. This list is populated with html5
	 * global attributes.
	 *
	 * @var array
	 */
	static protected $attrs = array(
		'accessKey',
		'class',
		'contextmenu',
		'dir',
		'draggable',
		'dropzone',
		'hidden',
		'id',
		'lang',
		'spellcheck',
		'style',
		'tabindex',
		'title'
	);

	/**
	 * @return	bool
	 */
	static public function exists($name)
	{
		if (! is_string($name)) {
			return false;
		}

		return in_array($name, self::$attrs);
	}

	/**
	 * @return array
	 */
	static public function get()
	{
		return self::$attrs;
	}
}
