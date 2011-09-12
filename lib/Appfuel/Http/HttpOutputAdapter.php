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


use Appfuel\Http\HttpResponse,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Output\AdapterHeaderInterface,
	Appfuel\Framework\Output\EngineAdapterInterface,
	Appfuel\Framework\Http\HttpResponseInterface,
	Appfuel\Framework\Http\HttpHeaderFieldInterface;

/**
 * Handle specific details for outputting http data
 */
class HttpOutputAdapter 
	implements EngineAdapterInterface, AdapterHeaderInterface
{
	/**
	 * Any php related functionality like sending headers is done here
	 * @var	HttpResponse
	 */
	protected $response = null;

	/**
	 * @param	HttpResponseInterface	$response
	 * @return	HttpOutputAdapter
	 */
	public function __construct(HttpResponseInterface $response = null)
	{
		if (null === $response) {
			$response = new HttpResponse();
		}
	
		$this->response = $response;
	}

	/**
	 * @param	array	$headers
	 * @return	HttpOutputAdapter
	 */
	public function addResponseHeaders(array $headers)
	{
		if (empty($headers)) {
			return;
		}

		$response = $this->getResponse();
		$response->loadHeaders($headers);
		return $this;
	}

	/**
	 * @param	string	$profile	name of the profile to load
	 * @return	HttpOutputAdapter
	 */
	public function loadHeaderProfile($profile)
	{
		return $this;
	}	

	/**
	 * @return	HttpResponseInterface
	 */
	public function getResponse()
	{
		return $this->response;	
	}

    /**
     * @param   mixed   $data
     * @param   string  $strategy
     * @return  mixed
     */
    public function output($data)
    {  
		$result   = null;
		$response = $this->getResponse();
		$response->setContent($data);
		$response->send();
        return;
    }
}
