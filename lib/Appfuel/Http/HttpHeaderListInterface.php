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
 * Manages a list of http headers
 */
interface HttpHeaderListInterface
{
	/**
	 * Returns the current header in the list
	 *
	 * @return	null
	 */
	public function getHeader();

	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$header
	 * @return	null
	 */
	public function addHeader($header);

	/**
	 * @return	return	array
	 */
	public function getAllHeaders();

	/**
	 * case insensitive search through the headers
	 * 
	 * @param	string	$header
	 * @return	bool
	 */
	public function isHeader($header);

	/**
	 * Load a list of headers into the header list
	 * 
	 * @param	array	 $headers
	 * @return	null
	 */
	public function loadHeaders(array $headers);
}
