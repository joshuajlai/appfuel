<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Console;


use InvalidArgumentException;

/**
 */
class ConsoleArgList
{
	/**
	 * List of original cli arguments (copy of the argv)
	 * @var array
	 */
	protected $list = array();

	/**
	 * @var array
	 */
	protected $short = array();
	
	/**
	 * @var array
	 */
	protected $long = array();

	/**
	 * @param	array	$args
	 * @return	ConsoleArgList
	 */
	public function __construct(array $args)
	{
		$this->list = $args;
		$result = $this->process($args);
	}

	/**
	 * @return	array
	 */
	public function getArgList()
	{
		return $this->list;
	}

	/**
	 * @param	array	$list
	 * @return	array
	 */
	protected function process(array $list)
	{
		array_shift($list);
		foreach ($list as $index => $item) {
			if (! is_string($item)) {
				$err = 'console argument must be a string';
				throw new DomainException($err);
			}

			if ('-' === $item{0}) {
				if ('-' === $item{1}) {
					echo "\n", print_r('long opt',1), "\n";

				}
				else {
					echo "\n", print_r('short opt',1), "\n";

				}
			}
			else {

			}
		}
		echo "\n", print_r($list,1), "\n";exit;
	}
}
