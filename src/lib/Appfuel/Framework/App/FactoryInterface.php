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
namespace Appfuel\Framework\App;

/**
 * Used to describe the methods needed in the factory to create 
 * the necessary objects used in starup, bootstrapping, dispatching and
 * Output rendering
 */
interface FactoryInterface
{
	/**
	 * Used to initialize the framework before startup
	 * 
	 * @param	string	$basePath	path to app root dir
	 * @return	InitializerInterface
	 */
	public function createAutoloader();
	public function createPhpError();
	public function createBootstrapper($type);
	public function createStartupStrategy($type);
	public function createFrontController();
	public function createDispatcher();
	public function createOutputEngine();
}
