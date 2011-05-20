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
namespace Appfuel\Framework\View;

use Appfuel\Framework\Data\DictionaryInterface;

/**
 * Interface needed by the framework to use view templates
 */
interface ViewInterface extends DictionaryInterface
{
    /**
     * Assign key value pair into scope
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  ViewInterface
     */
    public function assign($name, $value);

    /**
     * Convert the document contents to a string
     * @return string
     */
    public function build($data = null);
}
