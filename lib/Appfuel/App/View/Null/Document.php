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
namespace Appfuel\App\View\Null;

use Appfuel\Framework\View\ViewInterface,
	Appfuel\App\View\Data as ViewData;

/**
 * The null document is a dictionary that json encodes all its content
 */
class Document extends ViewData implements ViewInterface
{

	/**
	 * @param	string	$name 
	 * @param	mixed	$value
	 * @return	Document
	 */
	public function assign($name, $value)
	{
		return $this;
	}

	/**
	 * @return string
	 */
	public function build($data = null)
	{
		return '';
	}
}
