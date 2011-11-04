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
namespace Appfuel;

use Exception;

/**
 * The Exception is extended to use namespace an tags. Namespace allows the
 * namespace of the class using it instead of having to extend this class
 * just to have its namespace. Tags allow a list of space delimited keywords
 * which can be used by log aggregators limit searchs of errors
 */
class AppfuelException extends Exception
{
	/**
	 * Namespace of the class using the exception.
	 * @var string
	 */
	protected $namespace = '';

	/**
	 * Space delimited list of keywords for the exception
	 * @var
	 */
	protected $tags = '';

	/**
	 * @param	string			$message 
	 * @param	int				$code
	 * @param	ParentException	$prev
	 * @param	string			$namespace
	 * @param	string			$tags
	 * @return	Exception
	 */
	public function __construct($message = '', 
								$code = 0, 
								Exception $prev = null,
								$namespace = '',
								$tags = '')
	{
		parent::__construct($message, $code, $prev);
		
		if (is_string($namespace) && strlen($namespace) > 0) {
			$this->setNamespace($namespace);
		}

		if (is_string($tags) && strlen($tags) > 0) {
			$this->setTags($tags);
		}
	}

	/**
	 * @return	string
	 */
	final public function getNamespace()
	{
		return	$this->namespace;
	}

	/**
	 * @return	string
	 */
	final public function getTags()
	{
		return $this->tags;
	}

	/**
	 * @param	string	$ns
	 * @return	null
	 */
	final private function setNamespace($ns)
	{
		$this->namespace = trim($ns);
	}

	/**
	 * @param	string	$ns
	 * @return	null
	 */
	final private function setTags($tags)
	{
		$this->tags = trim($tags);
	}

	/**
	 * Append namespace and tags to the exception
	 *
	 * @return	string
	 */
	public function __toString()
	{
		$ns   = $this->getNamespace();
		$tags = $this->getTags();
	
		$msg  = $this->getMessage();
		$file = $this->getFile();
		$line = $this->getLine();
		$code = $this->getCode();

		$str = "exception [$ns] $msg in $file:$line:$tags";
		if (defined("AF_DEBUG") && true === AF_DEBUG) {
			$str = parent::__toString() . " $ns $tags";
		}

		return $str;
	}
}
