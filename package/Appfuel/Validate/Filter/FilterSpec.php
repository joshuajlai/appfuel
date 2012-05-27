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
namespace Appfuel\Validate\Filter;

use DomainException,
	InvalidArgumentException,
	Appfuel\DataStructure\Dictionary,
	Appfuel\DataStructure\DictionaryInterface;

/**
 * Value object used to hold information about a filter
 */
class FilterSpec implements FilterSpecInterface
{
	/**
     * name of the filter
	 * @var string
	 */
	protected $name = null;

	/**
     * List of paramters needed to run the filter
	 * @var DictionaryInterface
	 */
	protected $options = null;

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
		if (! isset($data['name'])) {
			$err = "filter name must be defined with key -(name)";
			throw new DomainException($err);
		}
		$this->setName($data['name']);

		$options = array();
		if (isset($data['options'])) {
			$options = $data['options'];
		}
		$this->setOptions($options);

		if (isset($data['error'])) {
			$this->setError($data['error']);
		}
	}

	/**
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return	array
	 */
	public function getOptions()
	{
		return $this->options;
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
	protected function setName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err  = "filter name must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->name = $name;
	}

	/**
	 * @param	array	$params	
	 * @return	null
	 */
	protected function setOptions($opts)
	{
		if (is_array($opts)) {
			$opts = new Dictionary($opts);
		}
		else if (! $opts instanceof DictionaryInterface) {
			$err  = "parameters must be an array or an object that implements ";
			$err .= "Appfuel\DataStructure\DictionaryInterface";
			throw new DomainException($err);
		}
		$this->options = $opts;
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
