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


use Appfuel\Http\HeaderList,
	Appfuel\Http\HttpResponse,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Output\AdapterHeaderInterface,
	Appfuel\Framework\Output\EngineAdapterInterface,
	Appfuel\Framework\Http\HttpResponseInterface,
	Appfuel\Framework\Http\HttpHeaderFieldInterface;

/**
 * Handle specific details for outputting http data
 */
class HttpOutputAdapter 
	implements AdapterEngineInterface, AdapterHeaderInterface
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
	 * @return	HttpResponseInterface
	 */
	public function getResponse()
	{
		return $this->reponse;	
	}

	/**
	 * @param	array	$headers	list of header field objects
	 * @return	null
	 */
	public function setHeaders(array $headers)
	{
		$response = $this->getResponse();
		foreach ($headers as $header) {
			if ($header instanceof HttpHeaderFieldInterface) {
				$response->addHeader($header);
			}
		}
	}

    /**
     * @param   string  $format 
     * @return  bool
     */
    public function isFormatSupported($format)
    {
        if (empty($format) || ! is_string($format)) {
            return false;
        }

        $format = strtolower($format);
        $supported = array('text', 'json', 'csv', 'html', 'bin');
        if (in_array($format, $supported)) {
            return true;
        }

        return false;
    }

    /**
     *  
     * @param   mixed   $data
     * @param   string  $strategy
     * @return  mixed
     */
    public function output($data, $strategy = 'render')
    {  
        if ('render' === $strategy) {
            $result = $this->render();
        } else {
            $result = $this->build();
        }

        return $result;
    }

    /**
     * @param   mixed   $data
     * @return  string
     */
    public function build($data)
    {  
        if (! $this->isValidOutput($data)) {
            return '';
        }

        if (is_scalar($data)) {
            return $data;
        }

        return $data->__toString();
    }


    /**
     * Render output to the stdout.
     * 
     * @param   mixed   $data
     * @return  null
     */
    public function render($data)
    {  
        if (! $this->isValidOutput($data)) {
			
        }
		
		echo $data;
    }

	public function read($data)
	{
		// some read operations
	}

	public function applyHeaders(array $headers = array())
	{
		if (empty($headers)) {
			return;
		}

	}

    /**
     * @param   string  $format
     * @return  null
     */
    public function renderFormatNotSupportedError($format)
    {
        $format =(string) $format;
        $error ="http ouput error: format -($format) is not supported";
    }

    /**
     * @param   mixed   $data
     * @return  bool
     */
    public function isValidOutput($data)
    {
        if (is_scalar($data) ||
            is_object($data) && method_exists($data, '__toString')) {
            return true;
        }

        return false;
    }
}
