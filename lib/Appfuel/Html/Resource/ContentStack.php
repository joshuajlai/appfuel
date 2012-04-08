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
namespace Appfuel\Html\Resource;

use InvalidArgumentException;

/**
 * Holds blocks of contents. Each block of content is a string
 */
class ContentStack implements ContentStackInterface
{
	/**
	 * @var array
	 */
	protected $data = array();

	/**
	 * @return	array
	 */
	public function getAll()
	{
		return $this->data;
	}

	/**
	 * @return	array
	 */
	public function getKeys()
	{
		return array_keys($this->data);
	}

	/**
	 * @param	string	$content
	 * @return	bool
	 */
	public function isContent($content)
	{
		if (! is_string($content)) {
			return false;
		}

		return isset($this->data[sha1($content)]);
	}

	/**
	 * Calculate the sha1 of the content to use as its key then store it
	 * @param	string	$content
	 * @return	ContentStack
	 */
	public function add($content)
	{
		if (! is_string($content)) {
			$err = 'content must be a empty string';
			throw new InvalidArgumentException($err);
		}
		
		$content = trim($content);
		$id = sha1($content);
		$this->data[$id] = $content;
		
		return $id;
	}


	/**
	 * @return	PackageFileLIst
	 */
	public function clear()
	{
		$this->data = array();
		return $this;
	}

	/**
	 * @return	int
	 */
	public function count()
	{
		return	count($this->data);
	}

	/**
	 * @return	int
	 */
	public function key()
	{
		return key($this->data);
	}

	/**
	 * @return	mixed
	 */
	public function current()
	{
		return current($this->data);
	}

	/**
	 * @return	null
	 */
	public function next()
	{
		next($this->data);
	}

	/**
	 * @return	null
	 */
	public function rewind()
	{
		reset($this->data);
	}

	/**
	 * @return	bool
	 */
	public function valid()
	{
		$key = $this->key();
		return is_string($key) && ! empty($key);
	}
}
