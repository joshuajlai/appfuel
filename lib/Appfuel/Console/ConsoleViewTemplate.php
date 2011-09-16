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

use Appfuel\Framework\Exception,
	Appfuel\View\ViewCompositeTemplate,
	Appfuel\View\Formatter\TextFormatter,
	Appfuel\Framework\Console\ConsoleViewTemplateInterface;

/**
 * Template used to generate generic html documents
 */
class ConsoleViewTemplate 
	extends ViewCompositeTemplate implements ConsoleViewTemplateInterface
{

    /**
     * @param   string  $templatePath   path to help template file (optional)
     * @param   PathFinderInterface $finder define the relative path (optional)
     * @param   array   $data   load data into the template (optional)
     * @param   ViewFormatterInterface $formatter formats the view (optional)
     * @return  ConsoleHelpTemplate
     */
    public function __construct($templatePath = null,
                                PathFinderInterface $finder = null,
                                array $data = null,
                                ViewFormatterInterface $formatter = null)
    {  
        
		if (null === $formatter) {
			/*
			 * first param:  character to delimit keys from values
			 * second param: character to delimit each array item
			 * third param:  parse only array values, ignore keys
			 */
			$formatter = new TextFormatter(' ', ' ', 'values');
			
		}
		parent::__construct($templatePath, $finder, $data, $formatter);
    }

}
