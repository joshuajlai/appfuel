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
	Appfuel\Framework\App\AppFactoryInterface;

/**
 * Responsible for creating objects required by the framework for 
 * initializaion, bootstrapping, dispatching and outputting.
 */
class AppFactory implements AppFactoryInterface
{
	/**
	 * @return	Init\Initializer
	 */
	public function createInitializer()
	{
		return new Init\Initializer();
	}

	/**
	 * @return string
	 */
	public function createUriString()
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
	public function createUri($uriString)
	{
		return	new PrettyUri($uriString);
	}

	/**
	 * @return	Request
	 */
	public function createRequest($uriString)
	{
		$uri = $this->createUri($uriString);
		return new Request($uri);
	}

	/**
	 * @param	array	$data	
	 * @return	Context
	 */
	public function createContext(array $data = array())
	{
		return new Context($data);
	}

	/**
	 * @return	Front
	 */
    static public function createFrontController()
	{
		return new FrontController();
	}

	/**
	 * @return	RenderEngine
	 */
    static public function createRenderEngine()
	{
		return new Render\Engine();
	}
}
