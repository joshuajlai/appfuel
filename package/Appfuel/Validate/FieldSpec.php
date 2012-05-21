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
 * Value object used to determine how a field is validated/filtered 
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

		if (isset($data['location'])) {
			$this->setLocation($data['location']);
		}

		if (! isset($data['filter'])) {
			$err  = "field -($field) must have a filter defined with key ";
			$err .= "-(filter)";
			throw new DomainException($err);
		}
		$this->setFilter($data['filter']);

		if (isset($data['params'])) {
			$this->setParams($data['params']);
		}

		if (isset($data['error'])) {
			$this->setError($data['error']);
		}
	}

	/**
	 * @return	string
	 */
	public function getFieldName()
	{
		return $this->field;
	}

	/**
	 * @return	string
	 */
	public function getLocation()
	{
		return $this->location;
	}

	/**
	 * @return	string
	 */
	public function getFilter()
	{
		return $this->filter;
	}

	/**
	 * @return	array
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * @return	string
	 */
	public function getErrors()
	{
		return $this->error;
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

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setLocation($loc)
	{
		if (! is_string($name)) {
			$err  = "the location of the field must be a string ";
			$err .= "string";
			throw new InvalidArgumentException($err);
		}

		$this->location = $name;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setFilter($name)
	{
		if (! is_string($name) || empty($name)) {
			$err  = "the filter name must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->filter = $name;
	}

	/**
	 * @param	array	$params	
	 * @return	null
	 */
	protected function setParams(array $params)
	{
		$this->params = $params;
	}

	/**
	 * @param	string	$text
	 * @return	null
	 */
	protected function setError($text)
	{
		if (! is_string($text)) {
			$err = "error message must be a string";
			throw new InvalidArgumentException($err);
		}
		
		$this->error = $text;
	}
}
