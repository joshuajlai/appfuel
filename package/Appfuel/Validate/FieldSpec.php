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

use DomainException,
	InvalidArgumentException;

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
	 * List of filter specifications
	 * @var	string
	 */
	protected $filters = array();

	/**
	 * Error to be prefixed to the aggregation of filter errors
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
		$this->setField($field);

		if (isset($data['location'])) {
			$this->setLocation($data['location']);
		}

		if (! isset($data['filters'])) {
			$err  = "field -($field) must have one or more filters defined ";
			$err .= "with key -(filters)";
			throw new DomainException($err);
		}
		$this->setFilters($data['filters']);

		if (isset($data['error'])) {
			$this->setError($data['error']);
		}
	}

	/**
	 * @return	string
	 */
	public function getField()
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
	public function getFilters()
	{
		return $this->filters;
	}

	/**
	 * @return	string
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setField($name)
	{
		if (! is_string($name) || empty($name)) {
			$err  = "field must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->field = $name;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setFilters(array $list)
	{
		$result = array();
		foreach ($list as $name => $data) {
			$data['name'] = $name;
			$this->filters[] = $this->createFilterSpec($data);
		}
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setLocation($loc)
	{
		if (! is_string($loc)) {
			$err  = "the location of the field must be a string";
			throw new InvalidArgumentException($err);
		}

		$this->location = $loc;
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

	/**
	 * @param	array	$data
	 * @return	FilterSpec
	 */
	protected function createFilterSpec(array $data)
	{
		return new FilterSpec($data);
	}
}
