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


use InvalidArgumentException;

/**
 * Manage http headers, protocol, and status. 
 */
class HttpResponse implements HttpResponseInterface
{
	/**
	 * List of headers to be sent 
	 * @var headerListInterface
	 */
	protected $headerList = null;

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
	 * Holds the details of the http status
	 * @var	HttpStatusInterface
	 */
	protected $status = null;

	/**
	 * @param	mixed	$data		content to be sent out
	 * @param	int		$status		status code of the response
	 * @param	array	$headers	list of header objects to be used
	 * @return	HttpResponse
	 */
	public function __construct($data = '',
								$status = 200,
								$version = '1.0',
								array $headers = null)
	{
		$this->setContent($data);
		
		$headerList = new HttpHeaderList();
		if (null !== $headers) {
			$headerList->loadHeaders($headers);
		}
		$this->setHeaderList($headerList);

		if (null === $version) {
			$version = '1.1';
		}
		$this->setProtocolVersion($version);
	
		if (null === $status) {
			$status = 200;
		}
		$this->setStatus($status);
	}

	/**
	 * @return	HttpHeaderListInterface
	 */
	public function getHeaderList()
	{
		return $this->headerList;
	}

	/**
	 * @param	HttpHeaderListInterface $list
	 * @return	null
	 */
	public function setHeaderList(HttpHeaderListInterface $list)
	{
		$this->headerList = $list;
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
			throw new InvalidArgumentException($err);
		}

		$this->content = (string) $data;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getContent()
	{
		return $this->content;
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

	/**
	 * @return	string
	 */
	public function getProtocolVersion()
	{
		return $this->version;
	}

	/**
	 * @return	HttpHeaderStatus
	 */
	public function getStatusLine()
	{
		return "HTTP/{$this->getProtocolVersion()} {$this->getStatus()}";
	}

	/**
	 * @return	int
	 */
	public function getStatusCode()
	{
		return $this->getStatus()
					->getCode();
	}

	/**
	 * @return	string
	 */
	public function getStatusText()
	{
		return $this->getStatus()
					->getText();
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
	public function setStatus($status)
	{
		if (! ($status instanceof HttpStatusInterface)) {
			$status = new HttpStatus($status);
		}	
        $this->status = $status;

		return $this;
	}

	/**
	 * @return	array
	 */
	public function getAllHeaders()
	{
		return $this->getHeaderList()
					->getAllHeaders();
	}

	/**
	 * @param	string $header
	 * @return	HttpResponse
	 */
	public function addHeader($header)
	{
		$this->getHeaderList()
			 ->addHeader($header);

		return $this;
	}

	/**
	 * @param	array	$headers
	 * @return	HttpResponse
	 */
	public function loadHeaders(array $headers) 
	{
		$this->getHeaderList()
			 ->loadHeaders($headers);

		return $this;
	}

	/**
	 * @param	string	$version
	 * @return	null
	 */
	protected function setProtocolVersion($version)
	{
		$valid = array('1.0', '1.1');

		if (is_int($version) || is_float($version)) {
			$version =(string) $version;
			if ('1' === $version) {
				$version = '1.0';
			}
		}
		
		if (empty($version) || 
			!is_string($version) || !in_array($version, $valid, true)) {
			$type = gettype($version);
			$err   = "Failed to instantiate HttpResponse: ";
			$err  .= "Can not set http protocol version must be one of the ";
			$err  .= "following strings '1.0' or '1.1' type given -($type) ";
			throw new InvalidArgumentException($err);
		}
		$this->version = $version;
	}

	/**
	 * @param	HttpStatus	$status
	 * @return	HttpResponse
	 */
	protected function updateStatusLineHeader()
	{
		$this->statusLine = new HttpHeaderField($statusLine);
		return $this;
	}
}
