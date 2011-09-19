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
namespace Appfuel\App\Context;

use Appfuel\Framework\Exception,
	Appfuel\Domain\Operation\Repository,
	Appfuel\Framework\App\Context\ContextUriInterface,
	Appfuel\Framework\App\Context\ContextInputInterface,
	Appfuel\Framework\App\Context\ContextBuilderInterface,
	Appfuel\Framework\Domain\Operation\OperationalRouteInterface;

/**
 * The context build holds all the logic for create uri strings, requests,
 * fetching the operational route, using all these objects to create the 
 * application context
 */
class ContextBuilder implements ContextBuilderInterface
{
	/**
	 * The uri parse token is ment for pretty uri's. It is a series of 
	 * characters not likely to occur in the url. It used to delineate where
	 * the route string ends and where get paramters begin. 
	 * @var string
	 */
	protected $uriParseToken = 'qx';

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
	 * @var	Error
	 */
	protected $exception = null;

	/**
	 * @return	string
	 */
	public function getUriParseToken()
	{
		return $this->uriParseToken;
	}

	/**
	 * @param	string	$token
	 * @return	ContextBuilder
	 */
	public function setUriParseToken($token)
	{
		if (! is_string($token)) {
			$this->setException(new Exception("parse token must be a string"));
			return false;
		}

		$this->uriParseToken = $token;
		return $this;
	}

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
	public function setUri(ContextUriInterface $uri)
	{
		$this->uri = $uri;
		return $this;
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
			$this->setException(new Exception("$err is not set"));
		}

		$uri = $_SERVER['REQUEST_URI'];
		if (! is_string($uri)) {
			$this->setException(new Exception("$err must be a valid string"));
		}

		$token = $this->getUriParseToken();
		return $this->setUri(new ContextUri($uri, $token));
	}

	/**
	 * @param	string	$uri
	 * @return	ContextBuilder
	 */
	public function useUriString($uri)
	{
		$err  = 'ConextBuilder failed: uri string given ';
		if (! is_string($uri)) {
			$this->setException(new Exception("$err must be a valid string"));
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
	public function setInput(ContextInputInterface $input)
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
			$this->setException(new Exception($err));
		}

		$uri = $this->getUri();
		if (! $uri instanceof ContextUriInterface) {
			$err .= 'default get params come from the context uri but the ';
			$err .= 'context uri has not been set';
			$this->setException(new Exception($err));
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
		return new ContextInput($method, $params);
	}

	/**
	 * @return	AppContext
	 */
	public function build()
	{
		$err = 'Build failed: ';
		$uri = $this->getUri();
		if (! $uri instanceof ContextUriInterface) {
			$err .= 'context uri has not been set';
			$this->setException(new Exception($err));
			return false;
		}

		$input = $this->getInput();
		if (! $input instanceof ContextInputInterface) {
			$err .= 'context input has not been set';
			$this->setException(new Exception($err));
			return false;
		}

		$repo = new Repository();
		$routeString = $uri->getRouteString();
		$opRoute = $repo->findOperationalRoute($routeString);
		if (! $opRoute instanceof OperationalRouteInterface) {
			$err .= "could not find route $routeString";
			$this->setException(new Exception($err));
			return false;
		}

		return new AppContext($uri, $opRoute, $input);
	}

	/**
	 * @return	bool
	 */
	public function isException()
	{
		return $this->exception instanceof Exception;
	}

	/**
	 * @return	Exception
	 */
	public function getException()
	{
		return $this->exception;
	}

	/**
	 * @param	Exception
	 */
	protected function setException(Exception $e)
	{
		$this->exception = $e;
	}
}
