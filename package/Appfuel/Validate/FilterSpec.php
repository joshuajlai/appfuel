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
	protected $params = null;

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

		$params = array();
		if (isset($data['params'])) {
			$params = $data['params'];
		}
		$this->setParams($params);

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
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * @return	bool
	 */
	public function isParams()
	{
		return $this->params instanceof DictionaryInterface;
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
	protected function setParams($params)
	{
		if (is_array($params)) {
			$params = new Dictionary($params);
		}
		else if (! $params instanceof DictionaryInterface) {
			$err  = "parameters must be an array or an object that implements ";
			$err .= "Appfuel\DataStructure\DictionaryInterface";
			throw new DomainException($err);
		}
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
