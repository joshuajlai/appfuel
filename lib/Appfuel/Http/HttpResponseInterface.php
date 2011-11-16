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

/**
 * Hold all the information about the http response. The HttpOutputAdapter uses
 * this interface to actually send the response. This interface is aware of
 * the http protocol used, the status of the response and manages a collection
 * of http headers 
 */
interface HttpResponseInterface
{
	/**
     * Assign the content to be used and convert it to a string in necessary
     * 
     * @param   mixed   scalar|object   $data
     * @return  HttpResponse
     */
    public function setContent($data);

    /**
     * @return  string
     */
    public function getContent();

	/**
	 * @return	string
	 */
	public function getProtocolVersion();

	/**
	 * This is the first http header to be sent out
	 *
	 * @return	HttpHeaderStatus
	 */
	public function getStatusLine();

	/**
	 * @return	int
	 */
	public function getStatusCode();

	/**
	 * @return	string
	 */
	public function getStatusText();

	/**
	 * @return	HttpStatusInterface
	 */
	public function getStatus();

	/**
	 * @param	int|string|HttpStatusInterface
	 * @return	HttpResponse
	 */
	public function setStatus($status);

	/**
	 * Returns a list of all headers as an array of strings
	 *
	 * @return	array
	 */
	public function getAllHeaders();

	/**
	 * @param	string $header
	 * @return	HttpResponse
	 */
	public function addHeader($header);

	/**
	 * @param	array	$headers
	 * @return	HttpResponse
	 */
	public function loadHeaders(array $headers); 

	/**
	 * @return	HttpHeaderListInterface
	 */
	public function getHeaderList();

	/**
	 * @param	HttpHeaderListInterface $list
	 * @return	null
	 */
	public function setHeaderList(HttpHeaderListInterface $list);
}
