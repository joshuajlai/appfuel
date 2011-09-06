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
	Appfuel\Framework\App\Context\ContextUriInterface;

/**
 * The uri represents the string making the request to the server. All requests
 * must have a uri string that holds at min the route information.
 */
class ContextUri implements ContextUriInterface
{
	/**
	 * Token used in the uri to determine where the route ends and where
	 * the parameters begin
	 * @var string
	 */
	protected $parseToken = null;

	/**
     * The original request uri string
     * @var string
     */
	protected $uri = NULL;

	/**
	 * The uri path is what Appfuel uses as its route string
	 * @var string
	 */
	protected $route = NULL;
	
	/**
	 * These could be http get parameters or cli parameters, both are 
	 * encoded into the uri string
	 * @var array
	 */
	protected $params = array();
	
	/**
	 * String consisting of only the parameters in pretty params. Note that
	 * even the query string '?param=value&param2=value2' will be converted 
	 * to the pretty format of param/value/param2/value2
	 * @var string
	 */
	protected $paramString = NULL;

    /**
     * Parse the orignal uri string into the client code, mvc string 
     * get parameters and parameter string of all get vars.
     *
     * @param   string  $requestString
     * @return  Uri
     */
    public function __construct($uriString, $parseToken = 'qx')
    {
		$err = 'Can not instantiate ContextUri:';
		if (empty($parseToken) || ! is_string($parseToken)) {
			throw new Exception("$err Parse token must be a non empty string");
		}
		$this->parseToken = $parseToken;

		if (! is_string($uriString)) {
			throw new Exception("$err uriString must be a string");
		}

		if (empty($uriString)) {
			$uriString = '/';
		}
		
		/* save the original uri string */
        $this->uri = $uriString;
		
        $result = $this->parseUri($uriString);
        $this->route  = $result['route'];
        $this->params = $result['params'];
        $this->paramString = $result['paramString'];
    }
	
	/**
	 * @return	string
	 */
	public function getParseToken()
	{
		return $this->parseToken;
	}

	/**
	 * @return string
	 */
	public function getUriString()
	{
		return $this->uri;
	}

	/**
	 * @return string
	 */
	public function getRouteString()
	{
		return $this->route;
	}

	/**
	 * @return array
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * @return string
	 */
	public function getParamString()
	{
		return $this->paramString;
	}

    /**
     * Parse Uri
     * Used to translate the request uri into module, controller, action
     * and parameters. Also deals with setting flags for when the uri is
     * incomplete
     *
     * @param   string  $requestUri
     * @return  array
     */
    protected function parseUri($uri)
    {
        $uri = ltrim($uri, "' ', '/'");

		$route  = null;
		$params	= array();
		$token	= $this->getParseToken();
		$paramString = '';
		if ($uri === $token) {
			$err  = "uri parse error: route can not the same as the uri parse ";
			$err .= "token ";
			throw new Exception($err);
		}
	
		/* empty uri string needs no processing */
		if (empty($uri) || '/' === $uri) {
			return array(
				'route'			=> '/',
				'params'		=> $params,
				'paramString'	=> $paramString
			);
		}
		
		/* process query string if it exists */
        $queryPos = strpos($uri, '?');
		if (false !== $queryPos) {
			$query   = substr($uri, $queryPos + 1, strlen($uri) - 1);
			$uri     = substr($uri, 0, $queryPos);
			$qparams = explode('&', $query);
			
			$prettyList = array();
			foreach ($qparams as $param) {
				$parts = explode('=', $param);

				/* only allow name value pairs */
				if (2 != count($parts)) {
					continue;
				}
				$key   = $parts[0];
				$value = $parts[1];

				$params[$key] = $value;
				$prettyList[] = $key;
				$prettyList[] = $value;
			}
			$paramString .= implode('/', $prettyList);
		}

		$tokenPos = strpos($uri, "/$token/");
		$tokenLen = strlen($token) + 2;
		
		/*
		 * parse token was not found with both forward slashs on each side 
		 * so look for parse token with out the forward slash on the right
		 * and pull out the route or just assign the whole string as the route
		 */
		if (false === $tokenPos) {
			$tokenPos = strpos($uri, "/$token");
			if (false === $tokenPos) {
				$route = $uri;
			} 
			else {
				$route = substr($uri, 0, $tokenPos);
			}
			
		}
		/* found parse token so separate route and parameters */
		else {
			$pstring = substr($uri, $tokenPos + $tokenLen, strlen($uri) - 1);
			$route   = substr($uri, 0, $tokenPos);
			$parts   = explode('/', trim($pstring, "' ', '/' "));

			/* convert /keyN/valueN/keyN+1/valueN+1.../keyN-1/valueN-1 */
			$key        = null;
			$lookAhead  = null;
			$value      = null;
			$max        = count($parts); 
			for($i = 0; $i < $max; $i += 2) {
				$key = $parts[$i];
				if (empty($key) || ! is_string($key)) {
					continue;
				}
				$lookAhead = $i + 1;
				if (array_key_exists($lookAhead, $parts)) {
					$value  = $parts[$lookAhead];
					$params[$key] = $value;
				}
				else {
					$params[$key] = null;
				}
			}
			
			/* append existing param string from query string */
			$paramString  = $pstring . '/' . $paramString;
		}

        $paramString = trim($paramString, "' ', '/'");

		/* last check to see if route is empty and turn it into the root
		 * route
		 */
		if (empty($route)) {
			$route = '/';
		}

        return array(
            'route'         => $route,
            'params'        => $params,
            'paramString'   => $paramString
        );
    }
}
