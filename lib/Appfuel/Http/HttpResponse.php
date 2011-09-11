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
class HttpResponse implements HttpResponseInterface
{
	/**
	 * List of headers to be sent 
	 * @var array
	 */
	protected $headers = array();

	/**
	 * Data to be sent in this response
	 * @var	string
	 */
	protected $content = null;

	/**
	 * Http protocal being used
	 * @var string
	 */
	protected $version = null;

	/**
	 * @param	mixed	$data		content to be sent out
	 * @param	int		$status		status code of the response
	 * @param	array	$headers	list of header objects to be used
	 * @return	HttpResponse
	 */
	public function __construct($data = '', $status = 200, $statusText = null)
	{
		$this->setContent($data);
		$this->setStatus(new HttResponseStatus($status, $statusText));
		$this->setProtocolVersion('1.0');

		if (! empty($headers)) {
			$this->loadHeaders($headers);
		}
	}

	/**
	 * @param	HttpHeaderFieldInterface	$header
	 * @return	HttpResponse
	 */
	public function addHeader(HttpHeaderFieldInterface $header)
	{
		$this->headers[] = $header;
		return $this;
	}

	/**
	 * @param	array	$headers
	 * @return	HttpResponse
	 */
	public function loadHeaders(array $headers) 
	{
		foreach ($headers as $idx => $header) {
			if (! instanceof HttpHeaderFieldInterface $header) {
				$type = gettype($header);
				$err  = "Can not load headers: header must be an object ";
				$err .= "that implments Appfuel\Framework\Http\HttpHeader";
				$err .= "FieldInterface. type given -($type) at index $idx";
				throw new Exception($err);
			}

			$this->addHeader($header);
		}

		return $this;
	}

	/**
	 * These headers are the headers to be sent
	 * @return	array
	 */
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	 * Assign the content to be used and convert it to a string in necessary
	 * 
	 * @param	mixed	scalar|object	$data
	 * @return	HttpResponse
	 */
	public function setContent($data)
	{
		if (! $this->isValidContent($data)) {
			$type = gettype($data);
			$err  = "Http response content must be a string or an object ";
			$err .= "implementing __toString(). parameter type -($type)";
			throw new Exception($err);
		}

		$this->content = (string) $data;
	}

	/**
	 * @return	string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @return	string
	 */
	public function getProtocolVersion()
	{
		return $this->version;
	}

	/**
	 * @param	string	$version	1.0 or 1.1
	 * @return	HttpResponse
	 */
	public function setProtocolVersion($version)
	{
		$valid = array('1.0', '1.1');
		if (empty($version) 
			|| ! is_string($version) || ! in_array($version, $valid)) {
			$type = gettype($version);
			$version = (string) $version;
			$err  .= "Can not set http protocol version: must be on of the ";
			$err  .= "following strings '1.0' or '1.1' type given -($type) "
			$err  .= "value given -($version)";
			throw new Exception($err);
		}

		$this->version = $version;
		return $this;
	}

	/**
	 * @return	int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param	HttpResponseStatus
	 * @return	HttpResponse
	 */
	public function setStatus(HttpResponseStatus $status)
	{
		$this->status = $status;
		return $this;
	}

	/**
	 * @param	mixed	$data
	 * @return	bool
	 */
	public function isValidContent($data)
	{
		if (null !== $data && 
			! is_scalar($data) && ! is_callable(array($data, '__toString'))) {
			return false;
		}

		return true;
	}
}
