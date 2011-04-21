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
namespace Appfuel\App;

use Appfuel\Framework\Request\UriInterface;

/**
 * The uri represents the string making the request to the server. All requests
 * must have a uri string that holds at min the route information.
 */
class PrettyUri implements UriInterface
{
	/**
     * The original request uri string
     * @var string
     */
	protected $uriString = NULL;

	/**
	 * The uri path is what Appfuel uses as its route string
	 * @var string
	 */
	protected $path = NULL;
	
	/**
	 * These could be http get parameters or cli parameters, both are 
	 * encoded into the uri string
	 * @var array
	 */
	protected $params = array();
	
	/**
	 * String consisting of only the parameters
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
    public function __construct($uriString)
    {
		if (empty($uriString)) {
			$uriString = '/';
		}

        $this->uriString = $uriString;
        $result = $this->parseUri($uriString);

        $this->path			= $result['path'];
        $this->params       = $result['params'];
        $this->paramString  = $result['paramString'];
    }

	/**
	 * @return string
	 */
	public function getUriString()
	{
		return $this->uriString;
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
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
        if (! is_string($uri)) {
            throw new Exception("Invalid uri: request uri must be a string");
        }
		
        $uri = ltrim($uri, "' ', '/'");

        /* 
         * parse any get parameters and add them to the param stack
         */
        $params  = array();
        $pstring = '';
        $getPos  = strpos($uri, '?');
        if (FALSE !== $getPos) {
            $getParams  = substr($uri, $getPos+1, strlen($uri) - 1);
            $uri        = substr($uri, 0, $getPos);
            $paramParts = explode('&', $getParams);
            $getParts   = array();
            foreach ($paramParts as $paramCombo) {
                $parts = explode('=', $paramCombo);
                /* only allow name value pairs */
                if (2 != count($parts)) {
                    continue;
                }
                $key            = $parts[0];
                $value          = $parts[1];
                $params[$key]   = $value;
                $getParts[]     = $key;
                $getParts[]     = $value;
            }
            $pstring        .= implode('/', $getParts);
		}

        $nchars  = substr_count($uri, '/', 0);

        if ($nchars >=0 && $nchars <=2) {
			$path = $uri;		
			if (empty($uri)) {
				$path = '/';
			}

            return array(
                'path'			=> $path,
                'params'		=> $params,
                'paramString'   => $pstring
            );

        }

        $parts = explode('/', $uri);
        if (count($parts) < 3) {
            throw new Exception("Invalid uri:
                invalid number of forward slashes (should not happen)"
            );
        }

        $module     = array_shift($parts);
        $submodule  = array_shift($parts);
        $action     = array_shift($parts);

        /* convert /key/value/.../key/value into an array */
        $max        = count($parts);
        $key        = NULL;
        $lookAhead  = NULL;
        $value      = NULL;
        for($i = 0; $i < $max; $i += 2) {
            $key = $parts[$i];

            $lookAhead = $i + 1;
            if (array_key_exists($lookAhead, $parts)) {
                $value  = $parts[$lookAhead];
                $params[$key] = $value;
            }
        }

        /* if ? was present then $pstring will have had parameters */
        $pstring = implode('/', $parts) . '/' . $pstring;
        $pstring = trim($pstring, "' ', '/'");
        return array(
            'path'          => "$module/$submodule/$action",
            'params'        => $params,
            'paramString'   => $pstring
        );
    }
}