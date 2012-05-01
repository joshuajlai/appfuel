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
namespace Appfuel\Validate\Filter;

use InvalidArgumentException;

/**
 * Define the failiure token to remove ambiguity from valid false and null 
 * cases. Also provide a default message for the validator to use if it does
 * not have a user defined message. 
 */
abstract class ValidateFilter implements FilterInterface
{
	/**
	 * Name is the string label used to identify this filter or sanitizer.
	 * It is used by the filter factory to create the filter and in default
	 * error messages
	 * @var string
	 */
	protected $name = null;

	/**
	 * Default error message to used when no other error messages 
	 * @var string
	 */
	protected $defaultError = 'Filter validation failure has occured for ';

	/**
	 * Flag used to indicate a failure has occured
	 * @var bool
	 */
	protected $isFailure = false;

	/**
	 * @param	string	$name	name of the filter used by the factory to
	 *							create it
	 * @return	VaidateFilter
	 */
	public function __construct($name)
	{
		if (empty($name) || ! is_string($name)) {
			throw new InvalidArgumentException(
				"Name must be a none empty string"
			);
		}

		$this->name = $name;
		$this->setDefaultError("Filter failure has occured for $name");
	}

	/**
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return	string
	 */
	public function getDefaultError()
	{
		return $this->defaultError;
	}

	/**
	 * @param	string	$error
	 * @return	ValidateFilter
	 */
	public function setDefaultError($error)
	{
		if (! is_string($error)) {
			throw new InvalidArgumentException(
				"default error must be a string"
			);
		}

		$this->defaultError = $error;
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isFailure()
	{
		return $this->isFailure;
	}

	/**
	 * @return	null
	 */
	protected function enableFailure()
	{
		$this->isFailure = true;
	}

	/**
	 * @return null
	 */
	protected function clearFailure()
	{
		$this->isFailure = false;
	}
}
