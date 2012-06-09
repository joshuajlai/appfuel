<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
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
	public function getFields();
	
	/**
	 * @param	string	$name
	 * @return	FieldValidatorInterface
	 */
	public function addField($name);

    /**
     * @return  FieldValidatorInterface
     */
    public function clearFields();

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
