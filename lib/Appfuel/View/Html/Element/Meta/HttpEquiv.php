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

use Appfuel\Framework\Exception;

/**
 * A specialized meta tag that deals only with http-equiv attributes for the
 * meta tag
 */
class HttpEquiv extends Tag
{
	/**
	 * Override the meta contructor to allow for only the charset attribute
	 *
	 * @return	Charset
	 */
	public function __construct($eq, $content)
	{
		$this->addValidAttributes(array('http-equiv','content'))
			 ->disableClosingTag();

		if (! $this->isValidString($eq) || ! $this->isValidString($content)) {
			throw new Exception("both attrs must be present");
		}

		$this->addAttribute('http-equiv', $encoding)
			 ->addAttribute('content', $content);
	}

    /**
     * Build only charset meta tags
     *
     * @return string
     */
    public function build()
    {
        if ($this->isValidHttpEquiv()) {
            return parent::build();  
        }
    }
}
