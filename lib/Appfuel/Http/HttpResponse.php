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
 * Value object used to wrap parameters php uses to send a header
 * with its header function
 */
class HttpResponse implements HttpResponseInterface
{
	/**
	 * List of headers to be sent 
	 * @var headerListInterface
	 */
	protected $headerList = null;

	/**
	 * This is the text of the first header to be send
	 * @var	HttpHeaderField
	 */
	protected $statusLine = null;

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
	public function __construct($data = '',
								$version = '1.0',
								HttpStatusInterface $status = null,
								array $headers = null)
	{
		$headerList = new HttpHeaderList();
		if (null !== $headers) {
			$headerList->loadHeaders($headers);
		}
		$this->setHeaderList($headerList);

		$this->setContent($data);

		$valid = array('1.0', '1.1');
		if (null === $version) {
			$version = '1.0';
		}
		elseif (is_numeric($version)) {
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

	
		if (null === $status) {
			$status = new HttpStatus();
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
	public function setStatus(HttpStatusInterface $status)
	{
		$this->status = $status;
		$this->updateStatusLineHeader();
		return $this;
	}

	/**
	 * @return	HttpHeaderStatus
	 */
	public function getStatusLineHeader()
	{
		return $this->statusLine;
	}

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
	 * @return null
	 */
	public function renderContent()
	{
		echo $this->content;
	}

	/**
	 * @return null
	 */
	public function sendHeaders()
	{
		if (headers_sent()) {
			return;
		}

		header($this->getStatusLine()->getField());
		
		$list = $this->getHeaderList();
		foreach ($list as $header) {
			header($header);
		}
	}

	/**
	 * @return null
	 */
	public function send()
	{
		$this->sendHeaders();
		$this->renderContent();
	}

	/**
	 * @param	HttpStatus	$status
	 * @return	HttpResponse
	 */
	protected function updateStatusLineHeader()
	{
		$statusLine = "HTTP/{$this->getProtocolVersion()} {$this->getStatus()}";
		$this->statusLine = new HttpHeaderField($statusLine);
		return $this;
	}
}
