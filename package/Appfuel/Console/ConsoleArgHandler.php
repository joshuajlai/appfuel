<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Console;


use InvalidArgumentException;

/**
 */
class ConsoleArgHandler
{
	/**
	 * @var array
	 */
	protected $original = array();

	/**
	 * @var array
	 */
	protected $args = array();

	/**
	 * @var array
	 */
	protected $long = array();

	/**
	 * @var array
	 */
	protected $short = array();

	/**
	 * @param	array	$args
	 * @return	ConsoleArgList
	 */
	public function __construct(array $data)
	{
		array_shift($data);
		$this->original = $data;
		$result = $this->processShortArgs($data);
		$this->args  = $result['args'];
		$this->long  = $result['long'];
		$this->short = $result['short'];
	}

	/**
	 * @return	array
	 */
	public function getOriginalList()
	{
		return $this->original;
	}

	/**
	 * @param	array	$list
	 * @return	array
	 */
	protected function process($args)
	{
		foreach ($args as $index => $arg) {
			if (! is_string($arg)) {
				$err = 'console argument must be a string';
				throw new DomainException($err);
			}

			if ('-' !== $arg{0}) {
				continue;
			}
			
		echo "\n", print_r($arg,1), "\n";exit;


		}
		echo "\n", print_r($list,1), "\n";exit;
	}
}
