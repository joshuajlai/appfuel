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


use DomainException;

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
	protected $isParam = false;

	/**
	 * Flag used by the console input to determine if this arg is required
	 * @var bool
	 */
	protected $isRequired = false;

	/**
	 * List of delimiters allowed to separate a parameter from this argument
	 * @var array
	 */
	protected $paramDelim = array('', ' ', '=');

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
			$this->setShortText($shortOpt);
		}
		
		$longOpt = null;
		if (isset($data['long'])) {
			$longOpt = $data['long'];
			$this->setLongText($longOpt);
		}

		if (! $this->isShortOption() || ! $this->isLongOption()) {
			$err  = "This argument spec must have at least one of the ";
			$err .= "following: -(short-opt,long-opt)";
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

		if (isset($data['param-delim'])) {
			$this->setParamDelims($data['param-delims']);
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
		return $this->shortOption;
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
	 * @param	string	$name
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
	 */
	protected function setShortOption($char)
	{
		if (! is_string($text) || strlen($char) !== 1) {
			$err = "short option must be a single character";
			throw new InvalidArgumentException($err);
		}

		$this->shortOpt = $name;
	}

	/**
	 * @param	string	$name
	 */
	protected function setLongOption($text)
	{
		if (! is_string($text) || strlen($text) < 1) {
			$err = "long option must be longer than a single character";
			throw new InvalidArgumentException($err);
		}

		$this->longOpt = $text;
	}



}
