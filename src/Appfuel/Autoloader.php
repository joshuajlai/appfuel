<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @package 	Appfuel
 * @author 		Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright	2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace 	Appfuel;

/**
 * Autoloader
 *
 * @package 	Appfuel
 */
class Autoloader
{
	/**
	 * Namespace Separator
	 * Used to resolve the incoming class name
	 * @var string
	 */
	protected $nsSeparator = NULL;

	/**
	 * Constructor
	 * Assign the namespace separator for PHP 5.3 
	 *
	 * @return 	Autoloader
	 */
	public function __construct()
	{
		$this->setNamespaceSeparator('\\');
	}

	/**
	 * @return 	string
	 */
	public function getNamespaceSeparator()
	{
		return $this->nsSeparator;
	}

	/**
	 * @param 	string 	$chars 	characters making up the namespace separator
	 * @return 	Autoloader
	 */
	public function setNamespaceSeparator($chars)
	{
		if (! is_string($chars)) {
			throw new \Exception(
				"Namespace separator must by a string"
			);
		}

		$this->nsSeparator = $chars;
		return $this;
	}

	/**
	 * Resolve Class Path
	 * Will convert PHP 5.3+ namespace separators to directory 
	 * separators
	 *
	 * @param 	string 	$className 	
	 * @return 	string
	 */
	public function resolveClassPath($className)
	{
		$ns = $this->getNamespaceSeparator();
		return str_replace($ns, DIRECTORY_SEPARATOR, $className);
	}
}

