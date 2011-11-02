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

use Appfuel\Framework\Exception;

/**
 * The Dependency loader will iterate through the namespace and file list. 
 * Each namespace is resolved and loaded with a require call. Each file is
 * is resolved and loaded with a require_once call.
 */
class NamespaceParser implements NamespaceParserInterface
{
	/**
	 * File extension used in final parsed string
	 * @var string
	 */
	protected $ext = '.php';

	/**
	 * @return	string
	 */
	public function getExtension()
	{
		return $this->ext;
	}

	/**
	 * @param	string	$ext
	 * @return	NamespaceParser
	 */
	public function setExtension($ext)
	{
		if (! is_string($ext)) {
			throw new Exception("extension must be a string");
		}

		$this->ext = $ext;
		return	$this;
	}

	/**
	 * Resolve php namespace first otherwise resolve as pear name 
	 *
	 * @param	string	$string	
	 * @return	string
	 */	
	public function parse($class, $isExtension = true)
	{
		$ext  = $this->getExtension();
		$path = $this->parseNs($class);
		if (false === $path && false === ($path = $this->parsePear($class))) {
			return false;
		}

		if (false === $isExtension) {
			$ext = '';
		}

		return "{$path}{$ext}";
	}

	/**
	 * Turn php namespace into a path using directory separator
	 *
	 * @param	string	$class
	 * @return	string | false on failure
	 */
	public function parseNs($class)
	{
		if (empty($class) || ! is_string($class) || !($class = trim($class))) {
			return false;
		}

		$dsep = DIRECTORY_SEPARATOR;
		$nsep = '\\';
			
		/* remove leading namespace char */
		if ($nsep === $class[0]) {
			$class = substr($class, 1);
		}
			
		$pos = strrpos($class, $nsep);
		if (false === $pos) {
			return false;
		}
		
		$namespace = substr($class, 0, $pos);
		$classname = substr($class, $pos + 1);
		return str_replace($nsep, $dsep, $namespace) . $dsep .
			   str_replace('_', $dsep, $classname);

	}

	/**
	 * Turn pear name into a path by replacing '_' with directory separator
	 *
	 * @param	string	$class
	 * @return	string | false on failure
	 */
	public function parsePear($class)
	{
		if (empty($class) || ! is_string($class) || !($class = trim($class))) {
			return false;
		}

		$dsep = DIRECTORY_SEPARATOR;
		$nsep = '_';
		return str_replace($nsep, $dsep, $class);
	}
}
