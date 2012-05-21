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

use InvalidArgumentException;

/**
 */
class FieldSpec implements FieldSpecInterface
{
	/**
	 * Name of the field to be validated
	 * @var string
	 */
	protected $field = null;

	/**
	 * Location of the field ex) get,post,put,delete,cli (long, short, args)
	 * @var string
	 */
	protected $location = null;

	/**
	 * Name of the filters to be used against this field
	 * @var	string
	 */
	protected $filter = null;

	/**
	 * Parameters needed by filter
	 * @var array
	 */
	protected $params = array();

	/**
	 * Error given back when filter fails
	 * @var string
	 */
	protected $error = null;


	/**
	 * @param	array	$data
	 * @return	FieldSpec
	 */
	public function __construct(array $data)
	{
		if (! isset($data['field'])) {
			$err = "validation field must be defined with key -(field)";
			throw new DomainException($err);
		}
		$field = $data['field'];
		$this->setFieldName($field);
	}

	/**
	 * @return	string
	 */
	public function getFieldName()
	{
		return $this->field;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setFieldName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err  = "the field name you are validating must be a non empty ";
			$err .= "string";
			throw new InvalidArgumentException($err);
		}

		$this->field = $name;
	}
}
