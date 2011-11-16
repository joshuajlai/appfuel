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


use Appfuel\Output\OutputAdapterInterface;

/**
 * Handle specific details for outputting http data
 */
class HttpOutputAdapter implements OutputAdapterInterface
{
    /**
     * @param   HttpResponseInterface|string   $data
     * @return  mixed
     */
    public function render($data)
    {
		if ($data instanceof HttpResponseInterface) {
			$response = $data;
		}
		else {
			$response = $this->createHttpResponse($data);
		}

		return $this->renderResponse($response);
    }

	/**
	 * @param	HttpResponseInterface $response
	 * @return	null
	 */
	public function renderResponse(HttpResponseInterface $response)
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

	/**
	 * @param	string	$msg
	 * @param	int		$code
	 * @return	null
	 */
	public function renderError($msg, $code = 500)
	{
		$this->renderResponse($this->createHttpResponse($msg, $code));
	}

	/**
	 * @param	string	$content
	 * @param	int		$code
	 * @return	HttpResponse
	 */
	protected function createHttpResponse($content, $code = null)
	{
		return new HttpResponse($content, $code);
	}
}
