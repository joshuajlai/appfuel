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
 * Handle specific details for outputting http data
 */
class HttpOutput implements HttpOutputInterface
{
	/**
	 * @param	HttpResponseInterface $response
	 * @return	null
	 */
	public function render(HttpResponseInterface $response)
	{
		if (headers_sent()) {
			return;
		}

		header($response->getStatusLine());
		
		$headerList = $reponse->getHeaderList();

		$replaceSimilar = false;
		foreach($headerList as $header) {
			header($header, $replaceSimilar);
		}
	}
}
