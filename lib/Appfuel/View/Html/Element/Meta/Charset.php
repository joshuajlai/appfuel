<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\View\Html\Element\Meta;

use InvalidArgumentException;

/**
 * A specialized meta tag that deals only with character encoding
 */
class Charset extends Tag
{
	/**
	 * Override the meta contructor to allow for only the charset attribute
	 *
	 * @return	Charset
	 */
	public function __construct($encoding)
	{
		$this->setTagName('meta')
			 ->addValidAttribute('charset')
			 ->addValidAttribute('http-equiv')
			 ->addValidAttribute('content')
			 ->disableClosingTag();

		if (! $this->isValidString($encoding)) {
			throw new InvalidArgumentException("Encoding must valid string");
		}

		$this->addAttribute('http-equiv', 'Content-Type')
			 ->addAttribute('content', 'text/html')
			 ->addAttribute('charset', $encoding);

	}
}
