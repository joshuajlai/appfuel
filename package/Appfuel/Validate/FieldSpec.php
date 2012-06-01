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
 * This is the base specification that all other specs must extend from. 
 * The field details has been left out because it is to be describe by more
 * specific specifications like UnaryFieldSpec or BinaryFieldSpec
 */
class FieldSpec implements FieldSpecInterface
{
	/**
	 * List of filter specifications used by the validator
	 * @var	string
	 */
	protected $filters = array();

	/**
	 * Key used to create the validator that will execute this specification
	 * @var string
	 */
	protected $validator = null;

	/**
	 * Key used to create the filter specification
	 * @var string
	 */
	protected $filterSpec = 'filter-spec';

	/**
	 * @param	array	$data
	 * @return	FieldSpec
	 */
	public function __construct(array $data)
	{
		if (! isset($data['validator'])) {
			$err = "validator name is required but not set, key -(validator)";
			throw new DomainException($err);
		}
		$this->setValidator($data['validator']);

		if (isset($data['filter-spec'])) {
			$this->setField($data['filter-spec']);
		}

		if (! isset($data['filters'])) {
			$err  = "field -($field) must have one or more filters defined ";
			$err .= "with key -(filters)";
			throw new DomainException($err);
		}
		$this->setFilters($data['filters']);
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
	public function getValidator()
	{
		return $this->validator;
	}

	/**
	 * @return	string
	 */
	public function getFilterSpec()
	{
		return $this->filterSpec;
	}

	/**
	 * @param	string	$key
	 * @return	null
	 */
	protected function setFilterSpec($key)
	{
		if (! is_string($key) || empty($key)) {
			$err  = "key used to id the filter spec must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->filterSpec = $key;
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
	protected function setValidator($name)
	{
		if (! is_string($name) || empty($name)) {
			$err  = "the name of the validator must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->validator = $name;
	}

	/**
	 * @param	array	$data
	 * @return	FilterSpec
	 */
	protected function createFilterSpec(array $data)
	{
		$key = $this->getFilterSpec();
		return ValidationFactory::createFilterSpec($key, $data);
	}
}
