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
namespace Appfuel\Html\Resource\Yui;

use InvalidArgumentException,
	Appfuel\Html\Resource\FileStack;

/**
 * Adds sorting based on yui3 after property
 */
class Yui3FileStack extends FileStack implements Yui3FileStackInterface
{
	/**
	 * List of files to be resorted
	 * @var array
	 */
	protected $after = array(
		'css' => array(),
		'js'  => array()
	);

	/**
	 * @param	string	$type
	 * @param	string	$file
	 * @param	string	$afterFile
	 * @return	FileStack
	 */
	public function addAfter($type, $file, $afterFile)
	{
		if (! is_string($type) || empty($type)) {
			$err = 'file type must be a none empty string';
			throw new InvalidArgumentException($err);
		}

		if (! is_string($file) || 
			empty($file) ||
			! is_string($afterFile) ||
			empty($afterFile)) {
			$err = 'file path must be a none empty string';
			throw new InvalidArgumentException($err);
		}

		$this->after[$type][$file] = $afterFile;
		return $this;
	}

	/**
	 * @param	string	$type
	 * @return	array
	 */
	public function getAfter($type)
	{
		if (! is_string($type) || empty($type)) {
			return false;
		}

		return $this->after[$type];
	}

	/**
	 * @return	Yui3FileStack
	 */
	public function sortByPriority()
	{
		if (count($this->after['js']) > 0) {
			$this->resolveAfter('js');
		}

		if (count($this->after['css']) > 0) {
			$this->resolveAfter('css');
		}

		return $this;
	}

	/**
	 * @param	string	$type	
	 * @return	Yui3FileStack
	 */
	public function resolveAfter($type)
	{
		if ('css' !== $type && 'js' !== $type) {
			$err = 'type must be css or js';
			throw new InvalidArgumentException($err);
		}

		$afterList = $this->getAfter($type);
		$list = $this->get($type);
		foreach ($afterList as $name => $after) {
			$namePos  = array_search($name, $list, true);
			$afterPos = array_search($after, $list, true);
			if (false === $afterPos || 
				false === $namePos  ||
				$namePos >= $afterPos) {
				continue;
			}
			$tmp  = array_splice($list, $namePos, 1);
			array_splice($list, $afterPos, 0, $tmp);
		}
		$this->files[$type] = $list;

		return $this;
	}
}
