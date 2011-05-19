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
namespace Appfuel\App\View;

use Appfuel\Framework\Exception,
	Appfuel\Framework\FileInterface,
	Appfuel\Framework\View\FileTemplateInterface;

/**
 * Handles all assignments of data to be used in a template
 */
class Builder
{
	/**
	 * @param	string	$type	type of document to create
	 * @return	false | DocumentInterface
	 */
	public function createDoc($type)
	{
		$type  = ucfirst($type);
		$valid = array('Html', 'Json', 'Cli', 'Csv', 'Null');
		if (! in_array($type, $valid)) {
			return false;
		}
	
		$class = __NAMESPACE__ . "\\{$type}\\Document";
		return new $class();
	}

	public function createHtmlView($viewInfo = null)
	{

	}

	public function build($type)
	{

	}

}
