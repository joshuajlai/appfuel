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
namespace Appfuel\Framework\Validate;

/**
 * Coordinator handles the movement of raw, clean, and error data so that
 * the other subsystems don't have to deal with it
 */
interface CoordinatorInterface
{
    /**
     * @return array
     */
    public function getAllClean();

    /**
     * @param   string  $label
     * @param   mixed   $value
     * @return  Coordinator
     */
    public function addClean($label, $value);

    /**
     * @param   string  $label
     * @param   mixed   $default
     * @return  mixed
     */
    public function getClean($label, $default = null);

    /**
     * @return array
     */
    public function getSource();

    /**
     * @param   mixed
     * @return  Validator
     */
    public function setSource($source);

    /**
     * @param   string  $label
     * @return  mixed
     */
    public function getRaw($label);

    /**
     * @param   string  $txt
     * @return  FilterValidator
     */
    public function addError($txt);
    
	/**
     * @return bool
     */
    public function isError();

    /**
     * @return string
     */
    public function getErrors();
}
