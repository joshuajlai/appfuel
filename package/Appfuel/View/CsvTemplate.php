<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\View;

/**
 * Convert data assignments into a comma separted list
 */
class CsvTemplate extends ViewTemplate
{
	/**
	 * @return	string
	 */
	public function build()
	{
		return ViewCompositor::composeCsv($this->getAll());
	}
}
