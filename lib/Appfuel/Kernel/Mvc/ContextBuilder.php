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
namespace Appfuel\Kernel\Mvc;

use RunTimeException,
	InvalidArgumentException;

/**
 * The context build holds all the logic for create uri strings, requests,
 * fetching the operational route, using all these objects to create the 
 * application context
 */
class ContextBuilder implements ContextBuilderInterface
{
    /**
     * Request Parameters. We parse the uri string and create our own parameters
     * instead of using super global $_GET. This is due to the way we use the 
     * url for holding mvc data plus key value pairs
     * @var array
     */
    protected $uri = null;

    /**
     * Method used for this request POST | GET
     * @var string
     */
    protected $input = null;

	/**
	 * @return	ContextUriInterface
	 */
	public function getUri()
	{
		return $this->uri;
	}

	/**
	 * @param	ContextUriInterface	$uri
	 * @return	ContextBuilder
	 */
	public function setUri(RequestUriInterface $uri)
	{
		$this->uri = $uri;
		return $this;
	}
	
	/**
	 * @param	string	$uriString
	 * @return	RequestUri
	 */
	public function createRequestUri($uriString)
	{
		return new RequestUri($uriString);	
	}

	/**
	 * Use the uri string from the server super global $_SERVER['REQUEST_URI']
	 * to create the uri and set it
	 *
	 * @return	ContextBuilder
	 */
	public function useServerRequestUri()
	{
		$err  = 'ConextBuilder failed: php super global ';
		$err .= '$_SERVER[\'REQUEST_URI\']';
		if (! isset($_SERVER['REQUEST_URI'])) {
			throw new RunTimeException("$err is not set");
		}

		$uri = $_SERVER['REQUEST_URI'];
		if (! is_string($uri)) {
			throw new RunTimeException("$err must be a valid string");
		}

		return $this->setUri($this->createRequestUri($uri));
	}

	/**
	 * @param	string	$uri
	 * @return	ContextBuilder
	 */
	public function useUriString($uri)
	{
		$err  = 'ConextBuilder failed: uri string given ';
		if (! is_string($uri)) {
			throw new RunTimeException("$err must be a valid string");
		}

		$token = $this->getUriParseToken();
		return $this->setUri(new ContextUri($uri, $token));
	}

	/**
	 * @return	ContextInputInterface
	 */
	public function getInput()
	{
		return $this->input;
	}

	/**
	 * @param	ContextInputInterface	$input
	 * @return	ContextBuilder
	 */
	public function setInput(AppInputInterface $input)
	{
		$this->input = $input;
		return $this;
	}

	/**
	 * Build a context input object from data in the php super globals.
	 *
	 * @return	ContextBuilder
	 */
	public function buildInputFromDefaults()
	{
		$method = 'cli';
		$err    = 'ConextBuilder failed:';
		if (isset($_SERVER['REQUEST_METHOD'])) {
			$method = $_SERVER['REQUEST_METHOD'];
		}
		
		if (empty($method) || ! is_string($method)) {
			$err .= 'request method is empty or not a string';
			throw new RunTimeException($err);
		}

		$uri = $this->getUri();
		if (! $uri instanceof RequestUriInterface) {
			$err .= 'default get params come from the context uri but the ';
			$err .= 'context uri has not been set';
			throw new RunTimeException($err);
		}

		$params = array();
		$params['get']    = $uri->getParams();
		$params['post']   = $_POST;
		$params['files']  = $_FILES;
		$params['cookie'] = $_COOKIE;
		$params['argv']   = array();
		if (isset($_SERVER['argv']) && is_array($_SERVER['argv'])) {
			$params['argv'] = $_SERVER['argv'];
		}

		return $this->setInput($this->createInput($method, $params));
	}

	/**	
	 * @param	string	$method
	 * @param	array	$params
	 * @return	ContextBuilder
	 */
	public function defineInputAs($method, array $params = array())
	{
		return $this->setInput($this->createInput($method, $params));
	}

	/**
	 * @param	string	$method	
	 * @param	array	$params	
	 * @return	ContextInput
	 */
	public function createInput($method, array $params = array())
	{
		return new AppInput($method, $params);
	}

	/**
	 * @return	AppContext
	 */
	public function build(array $list = null)
	{
		$err = 'Build failed: ';
		$uri = $this->getUri();
		if (! $uri instanceof UriInterface) {
			$err .= 'uri has not been set';
			throw new RunTimeException($err);
		}

		$input = $this->getInput();
		if (! $input instanceof RequestInputInterface) {
			$err .= 'context input has not been set';
			throw new RunTimeException($err);
		}

		$context = new AppContext($input);
		
	}
}
