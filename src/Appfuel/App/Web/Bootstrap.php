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
namespace Appfuel\App\Web;

use Appfuel\Framework\Exception,
	Appfuel\App\Factory;

class Bootstrap
{
    /**
     * Build the request used by the application. For the web get the 
     * the uri string from the http host
     *
     * @return  Request
     */
    public function buildRequest()
    {   
        $key = 'REQUEST_URI';
        if (! array_key_exists($key, $_SERVER) || empty($_SERVER[$key])) {
            $err = "Request uri is missing from the server super global " .
                   "and is required by the framework";
            throw new Exception($err);
        }

        $uriString = $_SERVER[$key];
        $uri = Factory::createUri($uriString);
        return Factory::createRequest($uri);
    }
}
