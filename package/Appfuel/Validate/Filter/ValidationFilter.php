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

use InvalidArgumentException,
	Appfuel\DataStructure\DictionaryInterface;

/**
 * Create the filter from the name given. In this case the 
 */
class ValidationFilter implements FilterInterface
{
	/**
	 * The name this filter was mapped with
	 * @var string
	 */
	protected $name = null;

	/**
	 * Dictionary of options used to control the filter's behavior
	 * @var DictionaryInterface
	 */
	protected $options = null;

	/**
	 * Message used when this filter fails
	 * @var string
	 */
	protected $error = null;

	/**
	 * @param	FilterSpecInterface $spec
	 * @return	ValidationFilter
	 */
	public function loadSpec(FilterSpecInterface $spec)
	{
		$this->setName($spec->getName());
		$options = $spec->getOptions();
		if ($options instanceof DictionaryInterface) {
			$this->setOptions($options);
		}

		$error = $spec->getError();
		if (! empty($error)) {
			$this->setError($error);
		}

		return $this;
	}

	/**
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param	string	$name
	 * @return	ValidationFilter
	 */
	public function setName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = "filter name must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->name = $name;
		return $this;
	}

	/**
	 * @return	ValidationFilter
	 */
	public function clearName()
	{
		$this->name = null;
		return $this;
	}

	/**
	 * @return	DictionaryInterface
	 */
	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * @param	DictionaryInterface	$options
	 * @return	ValidationFilter
	 */
	public function setOptions(DictionaryInterface $options)
	{
		$this->options = $options;
		return $this;
	}
	
	/**
	 * @return	bool
	 */
	public function isOptions()
	{
		return $this->options instanceof DictionaryInterface;
	}

	/**
	 * @param	string	$name
	 * @param	mixed	$default
	 * @return	mixed
	 */
	public function getOption($name, $default = null)
	{
		if (! $this->isOptions() || ! is_string($name)) {
			return $default;
		}

		return $this->getOptions()
					->get($name, $default);
	}

	/**
	 * @return	ValidationFilter
	 */
	public function clearOptions()
	{
		$this->options = null;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @param	string	$text
	 * @return	ValidationFilter
	 */
	public function setError($text)
	{
		if (! is_string($text)) {
			$err = "error text must be a string";
			throw new InvalidArgumentException($err);
		}

		$this->error = $text;
		return $this;
	}

	/**
	 * @return	ValidationFilter
	 */
	public function clearError()
	{
		$this->error = null;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getFailureToken()
	{
		return FilterInterface::FAILURE;
	}

	/**
	 * Can not have a abstract method and a defined interface. The interface 
	 * is more important so we forego the abstract class and throw a 
	 * LogicException instead
	 *
	 * @param	$raw	
	 * @return	false
	 */
	public function filter($raw)
	{
		throw new LogicException("should be extended");
	}
}
