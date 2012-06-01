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
class UnaryFieldSpec extends FieldSpec implements UnaryFieldSpecInterface
{
	/**
	 * Name of the field to be validated
	 * @var string
	 */
	protected $field = null;

	/**
	 * Location of the field ex) get, post or a method getter or property
	 * @var string
	 */
	protected $location = null;

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
	
		if (! isset($data['validator'])) {
			$data['validator'] = 'unary-validator';
		}

		if (isset($data['location'])) {
			$this->setLocation($data['location']);
		}

		parent::__construct($data);
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
	protected function setLocation($loc)
	{
		if (! is_string($loc)) {
			$err  = "the location of the field must be a string";
			throw new InvalidArgumentException($err);
		}

		$this->location = $loc;
	}
}
