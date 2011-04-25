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

use Appfuel\Framework\Exception,
	Appfuel\Framework\Env\Autoloader,
	Appfuel\Framework\Env\ErrorReporting,
	Appfuel\Framework\Env\ErrorDisplay,
	Appfuel\Framework\Env\TimeZone,
	Appfuel\Framework\Env\IncludePath,
	Appfuel\App\Render\Engine		  as RenderEngine,
	Appfuel\Framework\Web\Bootstrap	  as WebBootstrap,
	Appfuel\Framework\Cli\Boostrap    as CliBootstrap,
	Appfuel\Framework\Api\Bootstrap   as ApiBootstrap;

/**
 * Responsible for creating objects required by the framework for 
 * initializaion, bootstrapping, dispatching and outputting.
 */
class Factory
{
	/**
	 * Used to register spl autoloader for the framework
	 *
	 * @return	Autoloader
	 */
	static public function createAutoloader()
	{
		return new Autoloader();
	}

	/**
	 * @return	ErrorReporting
	 */
	static public function createErrorReporting()
	{
		return new ErrorReporting();
	}

	/**
	 * @return	ErrorDisplay
	 */
	static public function createErrorDisplay()
	{
		return new ErrorDisplay();
	}

	/**
	 * Used to change the php include_path
	 *
	 * @return IncludePath
	 */
	static public function createIncludePath()
	{
		return new IncludePath();
	}
	
	/**
	 * @return Timezone
	 */
	static public function createTimezone()
	{
		return new Timezone();
	}

	/**
	 * @return string
	 */
	static public function createUriString()
	{
		$key   = 'REQUEST_URI';
		if (! array_key_exists($key, $_SERVER) || empty($_SERVER[$key])) {
            $err = "Request uri is missing from the server super global " .
                   "and is required by the framework";
            throw new Exception($err);
        }

		return $_SERVER[$key];
	}

	/**
	 * @return Uri
	 */
	static public function createUri($uriString)
	{
		return	new PrettyUri($uriString);
	}

	/**
	 * @return	Request
	 */
	static public function createRequest()
	{
		$uriString = self::createUriString();
		$uri       = self::createUri($uriString);
		return new Request($uri);
	}

	static public function createErrorRoute()
	{
		$controller = 'Error\Handler\Invalid';
		$route      = 'error/handler/invalid';

		return new Route($route, $controller);
	}

	/**
	 * @throws	Exception
	 * @return	
	 */
    static public function createBootstrapper($type)
	{
		switch (strtolower($type)) {
			case 'web':
				return new WebBootstrap();
				break;
			case 'cli':
				return new CliBootstrap();
				break;
			case 'api':
				return new ApiBootstrap();
				break;
			default:
				throw new Exception("Invalid bootrap given as $type");
		}
	}

	/**
	 * @param	array	$data	
	 * @return	Message
	 */
	static public function createMessage(array $data = array())
	{
		return new Message($data);
	}

	/**
	 * @return	Front
	 */
    static public function createFrontController()
	{
		return new Front(self::createDispatcher(), self::createRenderEngine());
	}

	/**
	 * @return	Dispatcher
	 */
    static public function createDispatcher()
	{
		return new Dispatcher();
	}

	/**
	 * @return	RenderEngine
	 */
    static public function createRenderEngine()
	{
		return new RenderEngine();
	}
}
