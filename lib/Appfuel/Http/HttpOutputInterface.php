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
namespace Appfuel\Http;


use Appfuel\Kernel\OutputInterface;

/**
 * Handle specific details for outputting http data
 */
class HttpOutputInterface extends OutputInterface
{
	/**
	 * @param	HttpResponseInterface $response
	 * @return	null
	 */
	public function renderResponse(HttpResponseInterface $response);
}
