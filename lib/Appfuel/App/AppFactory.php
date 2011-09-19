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
	 * @return	ContextBuilder
	 */
	public function createContextBuilder()
	{
		return new Context\ContextBuilder();
	}

	/**
	 * @return	Front
	 */
    public function createFrontController()
	{
		return new FrontController();
	}

	/**
	 * @return	OutputEngine
	 */
    public function createOutputEngine()
	{
		return new OutputEngine();
	}

	/**
	 * @return	Filter\FilterManager
	 */
	public function createFilterManager()
	{
		return new Filter\FilterManager();
	}
}
