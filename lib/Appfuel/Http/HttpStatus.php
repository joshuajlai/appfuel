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
	Appfuel\Framework\Http\HttpStatusInterface;

/**
 * Value object used to wrap parameters php uses to send a header
 * with its header function
 */
class HttpStatus implements HttpStatusInterface
{
	/**
	 * Data to be sent in this response
	 * @var	string
	 */
	protected $code = null;

	/**
	 * @var	string
	 */
	protected $text = '';

	/**
	 * Http status codes to use a defaults
	 * @var array
	 */
	static protected $statusMap = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
	);

	/**
	 * @param	int		$status		status code of the response
	 * @return	HttpResponseStatus
	 */
	public function __construct($code = 200, $text = null)
	{
		if (! is_int($code) || $code < 100 || $code >= 600) {
			throw new Exception("invalid http response code");
		}
		
		$this->code = $code;
		if (null === $text) {
			$text = '';
			if (isset(self::$statusMap[$code])) {
				$text = self::$statusMap[$code];
			}
		}
			
		$this->text = $text;
	}

	/**
	 * @return	array
	 */
	public function getStatusMap()
	{
		return self::$statusMap;
	}

	/**
	 * @return	int
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @return	string
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * @return	string
	 */
	public function __toString()
	{
		return "{$this->getCode()} {$this->getText()}";
	}
}
