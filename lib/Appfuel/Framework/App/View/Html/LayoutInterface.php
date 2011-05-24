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
namespace Appfuel\Framework\App\View\Html;

use Appfuel\Framework\View\DocumentInterface;

/**
 * Interface needed by the framework to use view templates
 */
interface GridInterface extends DocumentInterface
{
    /**
     * Assign key value pair into scope
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  File
     */
    public function assignTo($location, $name, $value);

	public function loadTo($location, array $data);

    /**
     * Convert the document contents to a string
     * @return string
     */
    public function build();
}
