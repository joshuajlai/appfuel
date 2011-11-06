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
namespace Appfuel\Kernal;

use Appfuel\AppfuelException;

/**
 * The Framework object extended by any class that needs general framework
 * functionality like logging, exceptions, 
 */
class FrameworkObject implements FrameworkObjectInterface
{
	/**
	 * @var	AppfuelErrorInterface
	 */
	protected $afError = null;
	
	/**
	 * @return	bool
	 */
	public function isError()
	{
		return $this->afError instanceof AppfuelErrorInterface;
	}

	/**
	 * @param	AppfuelErrorInterface	$error
	 * @return	FrameworkObject
	 */
	public function setAppfuelError(AppfuelErrorInterface $error)
	{
		$this->afError = $error;
		return $this;
	}

	/**
	 * @return	AppfuelErrorInterface | null when not set
	 */
	public function getAppfuelError()
	{
		return $this->afError;
	}

	/**
	 * @param	string	$text	
	 * @param	scalar	$code
	 * @return	FrameworkObject
	 */
	public function setError($text, $code = 0) 
	{
		$this->afError = new AppfuelError($text, $code);
		return $this;
	}

	/**
	 * Alias to getAppfuelError
	 * 
	 * @return	AppfuelErrorInterface
	 */
	public function getError()
	{
		return $this->afError;
	}

	/**
	 * @throws	AppfuelException
	 * @param	string	$msg
	 * @param	scalar	$code
	 * @param	string	$tags
	 * @return	null
	 */
	public function throwException($msg, $code = 0, $tags = null)
	{
		throw new AppfuelException($msg, $code, null, get_class($this), $tags);
	}

	public function log($msg, $level)
	{
	
	}
}
