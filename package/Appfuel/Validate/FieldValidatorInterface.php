<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Validate;

use Appfuel\Validate\Filter\FilterInterface;

/**
 * All validators must extend from this interface
 */
interface FieldValidatorInterface extends ValidatorInterface
{
	/**
	 * @return	string
	 */
	public function getField();
	
	/**
	 * @param	string	$name
	 * @return	FieldValidatorInterface
	 */
	public function setField($name);

    /**
     * @return  FieldValidatorInterface
     */
    public function clearField();

    /**
     * @return  array
     */
    public function getFilters();

    /**
     * @param   FilterInterface $filter
     * @return  FieldValidatorInterface
     */
    public function addFilter(FilterInterface $filter);

    /**
     * @return  FieldValidatorInterface
     */
    public function clearFilters();

    /**
     * @param   FieldSpecInterface $spec
     * @return  FieldValidatorInterface
     */
    public function loadSpec(FieldSpecInterface $spec);

	/**
	 * @return	FieldValidatorInterface
	 */
	public function clear();
}
