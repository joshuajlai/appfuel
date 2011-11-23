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
namespace Appfuel\Console;

use Appfuel\View\ViewTemplate,
	Appfuel\View\Formatter\TextFormatter;

/**
 * Template used to generate generic html documents
 */
class ConsoleViewTemplate extends ViewTemplate
{
    /**
     * @param   array   $data   load data into the template (optional)
     * @return  ConsoleViewTemplate
     */
    public function __construct(array $data = null)
    {  
		/*
		 * first param:  character to delimit keys from values
		 * second param: character to delimit each array item
		 * third param:  parse only array values, ignore keys
		 */
		$formatter = new TextFormatter(' ', ' ', 'values');	
		parent::__construct( $data, $formatter);
    }

}
