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
namespace Appfuel\Framework\Doc;

use countable,
	SplFileInfo;

/**
 * The route is used by the dispatcher in order to build an controller
 * to execute
 */
interface ScopeInterface extends countable
{
    /**
     * Pulls data out of this scope. When the label is not found default is used 
     * instead.
     *
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
    public function render($label, $default = '', $sep = ' ');

    /**
     * Return all data in scope.
     * @return array
     */
    public function getAll();


    /**
     * Adds any label value pair into scope for use by template files.
     *
     * @param   string  $label  any string or object that supports __toString  
     * @param   mixed   $value
     * @return  NULL
     */
    public function assign($label, $value);

    /**
     * @param   string  $label
     * @return  bool
     */
    public function exists($label);

    /**
     * load an array of label value pairs. We foreach here
     * because we want to validate that each label is a proper string
     *
     * @param   array   $items
     * @return  void
     */
    public function load(array $items);

    /**
     * Merge the data of another scope into this one
     * 
     * @param   Scope   $scope
     * @return  void
     */
    public function merge(ScopeInterface $scope);

    /** 
     * returns the contents of the file specified as a string. This is used 
     * in conjuction with output buffering to produce a view template
     *
     * @param   File  $file path to template
     */
    public function build(SplFileInfo $file);

}
