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

use countable;

/**
 * The route is used by the dispatcher in order to build an controller
 * to execute
 */
interface TemplateInterface extends countable
{
    /**
     * Allows concrete implementations to add files
     * return void
     */
     public function initialize();

    /**
     * Assign key value pair into scope
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  File
     */
    public function assign($name, $value);

    /**
     * @param   string  $name
     * @return  bool
     */
    public function exists($name);

    /**
     * @return array
     */
    public function getAll();

    /**
     * @param   string  $name
     * @param   mixed   $default    returns when not found
     * @return  mixed
     */
    public function get($name, $default = NULL);

    /**
     * @param   string  $key
     * @param   string  $filename
     * @param   bool    $isLegacy
     * @return  TemplateInterface 
     */
    public function addFile($key, $file);

    /**
     * @param   string  $code
     * @return  bool
     */
    public function fileExists($code);

    /**
     * @return  mixed   FALSE|File
     */
    public function getFile($key);

    /**
     * Merge the scope into the file and then build a string
     * 
     * @param   string  $key
     * @return  string
     */
    public function buildFile($key);

    /**
     * Convert the document contents to a string
     * @return string
     */
    public function build();
}
