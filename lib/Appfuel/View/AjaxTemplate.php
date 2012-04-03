<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\View;

use InvalidArgumentException;

/**
 * The view template is the most basic of the templates. Holding all its data
 * in key/value pair it uses a formatter to convert it a string.
 */
class AjaxTemplate extends ViewTemplate implements AjaxInterface
{
	/**
	 * Code sent back to clientside to determine the status of the request
	 * @var scalar
	 */
	protected $statusCode = 200;

	/**
	 * message to describe the code
	 * @var	string
	 */
	protected $statusText = 'OK';

	/**
	 * @return	scalar
	 */
	public function getStatusCode()
	{
		return $this->statusCode;
	}

	/**
	 * @param	scalar	$code
	 * @return	JsonTemplate
	 */
	public function setStatusCode($code)
	{
		if (! is_scalar($code)) {
			$err = "Json status code must be a scalar value"
			throw new InvalidArgumentException($err);
		}

		$this->statusCode = $code;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getStatusText()
	{
		return $this->statusText;
	}

	/**
	 * @param	string
	 * @return	JsonTemplate
	 */
	public function setStatusText($text)
	{
		if (! is_string($text)) {
			throw new InvalidArgumentException("status text must be a string");
		}

		$this->statusText = $text;
		return $this;
	}

	/**
	 * @param	scalar	$code
	 * @param	string	$text
	 * @return	JsonTemplate
	 */
	public function setStatus($code, $text)
	{
		$this->setStatusCode($code)
			 ->setStatusText($text);

		return $this;
	}

	/**
	 * Clientside processing expects this datastructure back. To make assigns
	 * consistent I elected to defer generating the correct message format 
	 * until all the data has been assigned. All the original assignments are
	 * held now in data instead of being the root array.
	 *
	 * @return	string
	 */
    public function build()
	{
		return ViewCompositor::composeJson(array(
			'code'		=> $this->getStatusCode(),
			'message'	=> $this->getStatusText(),
			'data'		=> $this->getAll()
		));
	}
}
