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
namespace Appfuel\Framework;

/**
 * Handle command line specific logic for bootstapping the framework
 */
interface BootstrapInterface
{
	/**
	 * 
	 * @return array
	 */
	public function getRequestParams()
	{
		
	}

	public function getRequestUri()
	{
		
	}

	public function bootstrap(MessageInterface $msg)
	{
		return $msg;
	}
}
