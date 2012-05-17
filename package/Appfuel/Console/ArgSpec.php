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
namespace Appfuel\Console;


use DomainException,
	InvalidArgumentException;

/**
 */
class ArgSpec
{
	/**
	 * Name of the command line arg that the developer would refer to, not
	 * the name of the arg as seen on the command line
	 * @var string
	 */
	protected $name = null;
	
	/**
	 * character used for the short options of this argument
	 * @var string
	 */
	protected $shortOpt = null;

	/**
	 * text used for the long options of this argument
	 * @var string
	 */
	protected $longOpt = null;
	
	/**
	 * Text used in error message when this specification fails
	 * @var string
	 */
	protected $errorText = null;

	/**
	 * text used in help message for this arg
	 * @var string
	 */
	protected $helpText = null;

	/**
	 * Flag used to determine if this argument has a parameter
	 * @var bool
	 */
	protected $isParams = false;

	/**
	 * Flag used by the console input to determine if this arg is required
	 * @var bool
	 */
	protected $isRequired = false;

	/**
	 * List of delimiters allowed to separate a parameter from this argument
	 * @var array
	 */
	protected $paramDelims = array('', ' ', '=');

	/**
	 * @param	array	$data
	 * @return	ArgSpec
	 */
	public function __construct(array $data)
	{
		if (! isset($data['name'])) {
			$err = 'argument name must be given using the -(name) key';
			throw new DomainException($err);
		}
		$name = $data['name'];
		$this->setName($name);

		$shortOpt = null;
		if (isset($data['short'])) {
			$shortOpt = $data['short'];
			$this->setShortOption($shortOpt);
		}
		
		$longOpt = null;
		if (isset($data['long'])) {
			$longOpt = $data['long'];
			$this->setLongOption($longOpt);
		}

		if (! $this->isShortOption() && ! $this->isLongOption()) {
			$err  = "-($name) cmust have at least one of the ";
			$err .= "following options set: -(short,long)";
			throw new DomainException($err);
		}

		if (isset($data['help'])) {
			$this->setHelpText($data['help']);
		}

		$errorText  = "cli arg specification failed for -($name): ";
		$errorText .= "short option: -($shortOpt) long option: -($longOpt)";
		if (isset($data['error'])) {
			$errorText = $data['error'];
		}
		$this->setErrorText($errorText);

		if (isset($data['required']) && true === $data['required']) {
			$this->markAsRequired();
		}

		if (isset($data['allow-params']) && true === $data['allow-params']) {
			$this->enableArgParams();
		}

		if (isset($data['param-delims'])) {
			$this->setParamDelimiters($data['param-delims']);
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
	 * @return string
	 */
	public function getShortOption()
	{
		return $this->shortOpt;
	}

	/**
	 * @return	bool
	 */
	public function isShortOption()
	{
		return ! empty($this->shortOpt);
	}

	/**
	 * @return	bool
	 */
	public function isLongOption()
	{
		return ! empty($this->longOpt);
	}

	/**
	 * @return	string
	 */
	public function getLongOption()
	{
		return $this->longOpt;
	}

	/**
	 * @return string
	 */
	public function getErrorText()
	{
		return $this->errorText;
	}

	/**
	 * @return bool
	 */
	public function isHelpText()
	{
		return ! empty($this->helpText);
	}

	/**
	 * @return string
	 */
	public function getHelpText()
	{
		return $this->helpText;
	}

	/**
	 * @return bool
	 */
	public function isRequired()
	{
		return $this->isRequired;
	}

	/**
	 * @return	bool
	 */
	public function isParamsAllowed()
	{
		return $this->isParams;
	}

	/**
	 * @return	array
	 */
	public function getParamDelimiters()
	{
		return $this->paramDelims;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = "name of the argument must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->name = $name;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setShortOption($char)
	{
		if (! is_string($char) || 
			! ($char = trim($char)) ||
			strlen($char) !== 1) {
			$err = "short option must be a single character";
			throw new InvalidArgumentException($err);
		}

		$this->shortOpt = $char;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setLongOption($text)
	{
		if (! is_string($text) || strlen($text) < 1) {
			$err = "long option must be longer than a single character";
			throw new InvalidArgumentException($err);
		}

		$this->longOpt = $text;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setErrorText($text)
	{
		if (! is_string($text)) {
			throw new InvalidArgumentException("error text must be a string");
		}

		$this->errorText = $text;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setHelpText($text)
	{
		if (! is_string($text)) {
			throw new InvalidArgumentException("help text must be a string");
		}

		$this->helpText = $text;
	}

	/**
	 * @return null
	 */
	protected function markAsRequired()
	{
		$this->isRequired = true;
	}

	/**
	 * @return null
	 */
	protected function enableArgParams()
	{
		$this->isParams = true;
	}

	/**
	 * @param	array	$delims
	 * @return	null
	 */
	protected function setParamDelimiters(array $delims)
	{
		foreach ($delims as $delim) {
			if (! is_string($delim)) {
				$err = "parameter delimiter must be a string";
				throw new DomainException($err);
			}
		}

		$this->paramDelims = $delims;
	}
}
