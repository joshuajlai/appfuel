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
namespace Appfuel\MsgBroker\Amqp;

use Appfuel\Framework\Exception;

/**
 * Appfuel is currently wrapping the php-amqplib project found out
 * https://github.com/tnc/php-amqplib.git I plan on replacing this code 
 * with my own module but I need some working code So I am using this
 * lib exposed through a facade that I control. The dependency will load
 * all the files so that we will not need to use the auto loader.
 */
class Dependency
{
	/**
	 * @var array
	 */
	protected $files = array();

	/**
	 * Ensure lib path exists and setup absolute path of files to be included
	 *
	 * @return	Dependency
	 */
	public function __construct()
	{
		if (! defined('AF_LIB_PATH')) {
			$err = "can not load dependencies without constant AF_LIB_PATH";
			throw new Exception($err);
		}

		$dir = AF_LIB_PATH . '/Appfuel/MsgBroker/Amqp/php-amqplib';

		$this->files = array(
			"{$dir}/hexdump.inc",
			"{$dir}/amqp_wire.inc",
			"{$dir}/amqp.inc",
		);
	}

	/**
	 * @return array
	 */
	public function getFiles()
	{
		return $this->files;
	}

	/**
	 * These are dependencies, operations can not continue without
	 * them, we fail hard because there is no point continuing 
	 * 
	 * @throws	\Exception
	 * @param	array	$files
	 * @return	null
	 */
	public function requireFiles(array $files)
	{
		foreach ($files as $file) {
			if (! file_exists($file)) {
				throw new \Exception(
					"Required file could not be found $file"
				);
			}

			require_once $file;
		}
	}

	/**
	 * get and require the dependent files
	 *
	 * @return	null
	 */
	public function load()
	{
		$this->requireFiles($this->getFiles());
	}
}
