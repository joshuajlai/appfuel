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
namespace Appfuel\View;

use Exception,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\View\Compositor\CsvCompositor;

/**
 * The Csv Template operates like a standard template but uses a csv
 * compositor for rendering.  The usage for the csv template would be to 
 * load the final result set into the template instead of assigning one
 * record at a time.
 */
class CsvTemplate extends ViewTemplate
{
	/**
	 * @param	mixed	$file 
	 * @param	array	$data
	 * @return	CsvTemplate
	 */
	public function __construct(array $data = null) 
	{
	    parent::__construct($data, new CsvCompositor());
    }
}
