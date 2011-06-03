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
namespace Appfuel\Framework\App\View;

use countable;

/**
 * The route is used by the dispatcher in order to build an controller
 * to execute
 */
interface ScopeInterface extends countable
{
    /**
     * @param   string  $label      data label 
     * @param   mixed   $default    value returned used when data not found
     * @return  mixed
     */
    public function get($label, $default = NULL);

    /**
     * echo the value found at label or default if nothing is found. 
     * When value at label is an array try to implode with given separator.
     * When value at label is an object that supports __toString then
     * use that magic method on the object
     * 
     * @param   string  $label
     * @param   mixed   $default    char used when lable is not found
     * @param   mixed   $sep        char used as separtor for array types
     * @return string
     */
    public function render($label, $default = '');

    /**
     * Return all data in scope.
     * @return array
     */
    public function getAll();

    /**
     * @param   string  $label
     * @return  bool
     */
    public function exists($label);

    /** 
     * returns the contents of the file specified as a string. This is used 
     * in conjuction with output buffering to produce a view template
     *
     * @param	mixed	string|SplFileInfo	$file path to template
	 * @return	string
     */
    public function build($file);

}
