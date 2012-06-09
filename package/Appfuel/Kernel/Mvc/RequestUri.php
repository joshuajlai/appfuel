<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
 */
namespace Appfuel\Kernel\Mvc;

use InvalidArgumentException;

/**
 * The uri represents the string making the request to the server. In general,
 * the context uri is retrieved from $_SERVER['REQUEST_URI'], but the actual
 * value is supplied by the context builder so as far as the context uri is
 * concerned any string given as the first paramter is the uri string. This
 * allows the uri to be used in the command line, web and api without 
 * modification.
 */
class RequestUri implements RequestUriInterface
{
	/**
     * The original request uri string
     * @var string
     */
	protected $uri = null;

	/**
	 * The uri path is what Appfuel uses as its route string
	 * @var string
	 */
	protected $route = null;

	/**
	 * @var string
	 */
	protected $routeFormat = null;
	
	/**
	 * These could be http get parameters or cli parameters, both are 
	 * encoded into the uri string
	 * @var array
	 */
	protected $params = array();
	
	/**
	 * String consisting of only the parameters in pretty params. Note that
	 * even the query string '?param=value&param2=value2' will be converted 
	 * to the pretty format of param/value/param2/value2 and the route key
	 * will always be removed so you can attact it to another route easily
	 *
	 * @var string
	 */
	protected $paramString = null;

    /**
     * Parse the orignal uri string into the client code, mvc string 
     * get parameters and parameter string of all get vars.
     *
     * @param   string  $requestString
     * @return  Uri
     */
    public function __construct($uri)
    {
		$err = 'uri string must be a string';
		if (! is_string($uri)) {
			throw new InvalidArgumentException($err);
		}

        $this->uri = trim($uri);
		
        $result = $this->parseUri($this->uri);

        $this->route  = $result['route-key'];
		$this->routeFormat = $result['route-format'];
        $this->params = $result['params'];
        $this->paramString = $result['paramString'];
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
	public function getRouteKey()
	{
		return $this->route;
	}

	/**
	 * @return	string
	 */
	public function getRouteFormat()
	{
		return $this->routeFormat;
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
        $uri = ltrim($uri, "'/'");
		if (empty($uri)) {
			return array(
				'route-key'		=> '',
				'route-format'  => null,
				'params'		=> array(),
				'paramString'	=> ''
			);
		}
		
		$isRoute     = false;
		$route		 = '';
        $queryPos	 = strpos($uri, '?');
		$queryParams = array();
		$prettyQuery = '';
		/* process query string if it exists */
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
				$key   = trim(urldecode($key));
				if (empty($key)) {
					continue;
				}
				/* reserved work by framework to indicated the route key
				 * in the query string
				 */
				if ($key === 'routekey') {
					$route = $value;
					$isRoute = true;
					continue;
				}

				$queryParams[$key] = $value;
				$prettyQuery .= "$key/$value/";
			}
			$prettyQuery = trim($prettyQuery, "'/'");
		}

		$parts = explode('/', trim($uri, "' ', '/' "));

		/* no route found an no path to look for route */
		if (empty($parts)) {
			$route = trim(urldecode($route));
			$result = array(
				'params'		=> $params,
				'paramString'	=> $prettyQuery
			);
			return array_merge($result, $this->parseRouteKey($route));
		}

		/*
		 * if the route key was not found in the query string then it must
		 * be the first parameter of the uri path so remove it and process
		 * the rest of the parameters ans /key/value 
		 */
		if (false === $isRoute) {
			$route = array_shift($parts);
		}

		$max = count($parts);
		$pathParams = array();
		$pathString = '';
		$key        = null;
		$lookAhead  = null;
		$value      = null;
		for($i = 0; $i < $max; $i += 2) {
			if (! isset($parts[$i])) {
				continue;
			}

			$key = $parts[$i];
			$key = trim(urldecode($key));
			if (empty($key) || ! is_string($key)) {
				continue;
			}
			$lookAhead = $i + 1;
			if (array_key_exists($lookAhead, $parts)) {
				$value  = $parts[$lookAhead];
				$pathParams[$key] = $value;
				$pathString .= "$key/$value/";
			}
			/* catch on the case when param exists but has no lookAhead 
			 * because the forward slash was trimmed off. 
			 * ex) my-route/qt/param1
			 */
			else {
				$pathParams[$key] = null;
				$pathString .= "$key//";
			}
		}
		$pathString = trim($pathString, "'/'");
	
		/* append existing param string from query string */
        $paramString = trim("$pathString/$prettyQuery", "' ', '/'");

		
		$params = array_merge($pathParams, $queryParams);	
		$route  = trim(urldecode($route));
        $result = array(
            'params'        => $params,
            'paramString'   => $paramString
        );

		return array_merge($result, $this->parseRouteKey($route));
    }

	public function parseRouteKey($key)
	{
        $parts  = explode('.', $key);
        $key    = current($parts);
        $format = strtolower(next($parts));

		return array(
			'route-key'	   => $key,
			'route-format' => $format
		);
	}

}
