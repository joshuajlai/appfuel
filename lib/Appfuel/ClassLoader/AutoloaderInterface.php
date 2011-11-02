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
namespace Appfuel\ClassLoader;

/**
 * The autoloader decouples the parsing of the namespace by using a parser.
 * Its only responsibilities are checking if the file exists against a list
 * of paths.
 */
interface AutoloaderInterface
{
	/**
	 * @return	NamespaceParserInterface
	 */
	public function getParser();

	/**
	 * @param	NamespaceParserInterface $parser
	 * @return	StandardAutoLoader
	 */
	public function setParser(NamespaceParserInterface $parser);

	/**
	 * @return	bool
	 */
	public function isIncludePathEnabled();

	/**
	 * @return	StandardAutoLoader
	 */
	public function enableIncludePath();

	/**
	 * @return	StandardAutoLoader
	 */
	public function disableIncludePath();

	/**
	 * @param	string	$namespace	
	 * @param	string	absolute path to namespace
	 * @return	StandardAutoLoader
	 */
	public function addPath($path);

	/**
	 * @return	array
	 */
	public function getPaths();

	/**
	 * @param	bool	$flag
	 * @return	null
	 */
	public function register($prepend = false);

	/**
	 * @param	string
	 * @return	null
	 */
	public function loadClass($class);
}
