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
namespace Appfuel\Http;


use Appfuel\Framework\Exception,
	Appfuel\Framework\Http\HttpHeaderFieldInterface;

/**
 * Value object used to wrap parameters php uses to send a header
 * with its header function
 */
class HttpHeaderField implements HttpHeaderFieldInterface
{
	/**
	 * Header text
	 * @var string
	 */
	protected $field = null;

	/**
	 * Flag used to indicate whether the header should replace a previous 
	 * header or add a second header of the same type.
	 * @var bool
	 */
	protected $isReplace = true;

	/**
	 * Force the http reponse code to the specified value.
	 * @var string
	 */
	protected $code = null;
	
	/**
	 * @param	HttpResponseInterface	$response
	 * @return	HttpOutputAdapter
	 */
	public function __construct($text, $replace = true, $code = null)
	{
		if (empty($text) || ! is_string($text)) {
			throw new Exception("header text can not be empty");
		}
		$this->field = $text;

		/*
		 * defaults to true so only need to check for the false case 
		 */
		if (false === $replace) {
			$this->isReplace = false;
		}

		if (null !== $code && (! is_int($code) || $code < 0)) {
			throw new Exception("header status code must be an int > 0");
		}
		$this->code = $code;
	}

	/**
	 * @return	HttpResponseInterface
	 */
	public function getField()
	{
		return $this->field;	
	}

    /**
     * @return  bool
     */
    public function isReplace()
    {
        return $this->isReplace;
    }

    /**
     * @return  null | int
     */
    public function getCode()
    {  
        return $this->code;
    }

	/**
	 * @return	string
	 */
	public function __toString()
	{
		return $this->getField();
	}
}
