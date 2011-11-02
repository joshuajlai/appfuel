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
 * Parse a namespace into a file path
 */
interface NamespaceParserInterface
{
	/**
	 * @param	string
	 */
	public function getExtension();

	/**
	 * @param	string
	 */
	public function setExtension($ext);

	/**
	 * Resolve php namespace first otherwise resolve as pear name 
	 *
	 * @param	string	$string	
	 * @return	string
	 */	
	public function parse($class, $isExtension = true);
}
