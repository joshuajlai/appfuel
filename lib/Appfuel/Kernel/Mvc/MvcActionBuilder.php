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

use LogicException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\KernelRegistry,
    Appfuel\ClassLoader\StandardAutoLoader,
    Appfuel\ClassLoader\AutoLoaderInterface;

/**
 */
class MvcActionBuilder implements MvcActionBuilderInterface
{
	/**
	 * The class name used to create the action controller class found in
	 * the namespace the route maps too
	 * @var	string
	 */
	static protected $actionClassName = 'ActionController';

	/**
	 * We reuse the autoloader class to parse the namespace into a dir path
	 * to find the mvc action, view, and route detail.
	 * @var AutoLoaderInterface
	 */
	protected $loader = null;

    /**
	 * The uri is designed to hold the route key and optionally get parameters
	 * for an http get request. 
     * @var RequestUriInterface
     */
    protected $uri = null;

    /**
     * @param   string  $controllerClass
     * @return  MvcActionBuilder
     */
    public function __construct(AutoLoaderInterface $loader = null)
    {
        /*
         * Note that we use the load class from the lib directory. This 
         * constant is set during intialization. I will refactor next to a 
         * a path finder. (on a deadline right now) --rsb
         */
        if (null === $loader) {
            $loader = new StandardAutoLoader(AF_LIB_PATH);
        }
        $this->setClassLoader($loader);
    }

	/**
	 * @param	string	$name
	 * @return	null
	 */
	static public function setActionClassName($name)
	{
		if (! is_string($name) || ! ($name = trim($name))) {
			$err = 'class name must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		self::$actionClassName = $name;
	}

	/**
	 * @return	string
	 */
	static public function getActionClassName()
	{
		return self::$actionClassName;
	}

	/**
	 * @return	AutoLoaderInterface
	 */
	public function getClassLoader()
	{
		return $this->loader;
	}

	/**
	 * @param	AutoLoaderInterface $loader
	 * @return	MvcActionBuilder
	 */
	public function setClassLoader(AutoLoaderInterface $loader)
	{
		$this->loader = $loader;
		return $this;
	}

    /**
     * @return  RequestUriInterface
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param   RequestUriInterface $uri
     * @return  MvcActionBuilder
     */
    public function setUri($uri)
    {
		if (is_string($uri)) {
			$uri = $this->createUri($uri);
		} 
		else if (! $uri instanceof RequestUriInterface) {
			$err  = 'uri must be a string or an objec the implements the ';
			$err .= 'Appfuel\Kernel\Mvc\RequestUriInterface';
			throw new InvalidArgumentException($err);
		}

        $this->uri = $uri;
        return $this;
    }

    /**
     * Use the uri string from the server super global $_SERVER['REQUEST_URI']
     * to create the uri and set it
     *
     * @return  ContextBuilder
     */
    public function useServerRequestUri()
    {
        $err  = 'php super global $_SERVER[\'REQUEST_URI\'] is not set';
        if (! isset($_SERVER['REQUEST_URI'])) {
            throw new RunTimeException($err);
        }

        $uri = $_SERVER['REQUEST_URI'];

		$err = 'request uri found in $_SERVER is not a string';
        if (! is_string($uri)) {
            throw new LogicException($err);
        }

        return $this->setUri($uri);
    }

    /**
     * @param   string  $uriString
     * @return  RequestUri
     */
    public function createUri($uriString)
    {
        return new RequestUri($uriString);
    }


}
