<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\View\Html\Tag;

use InvalidArgumentException;

/**
 * Encapsulates the handing of the content of tag, this includes adding 
 * content, getting content and converting content into a stirng
 */
class TagContent implements TagContentInterface
{
	/**
	 * Used to separate content
	 * @var string
	 */
	protected $sep = ' ';

	/**
	 * Used to hold the contents of the tag
	 * @var array
	 */ 
	protected $data = array();

	/**
	 * @param	mixed	string|array	$data	cotent
	 * @param	string					$char	content separator
	 * @return	TagContent
	 */
	public function __construct($data = null, $char = null)
	{
		if (null === $char) {
			$char = ' ';
		}
		$this->setSeparator($char);

		if (null !== $data) {
			if (is_string($data)) {
				$this->add($data);
			}
			else if (is_array($data)) {
				$this->load($data);
			}
			else {
				$err  = 'data must be a string (single content block) or ';
				$err .= 'an array (list of content blocks) ';
				throw new InvalidArgumentException($err);
			}
		}
	}

	/**
	 * @return string
	 */
	public function getSeparator()
	{
		return $this->sep;
	}

	/**
	 * @param	scalar	$char
	 * @return	Tag
	 */
	public function setSeparator($char)
	{
		if (! is_scalar($char)) {
			$err = 'content separator must by a scalar value';
			throw new InvalidArgumentException($err);
		}

		$this->sep =(string) $char;
		return $this;
	}

	public function load(array $list)
	{
		foreach ($list as $item) {
			if (is_string($item)) {
				$this->add($item);
			}
			elseif (is_array($item) && isset($item[0]) && isset($item[1])) {
				$this->add($item[0], $item[1]);
			}
		}
	}

	/**
	 * Add content to the tag
	 * 
	 * @param	mixed	$data	
	 * @param	string	$action		what to do with the content
	 * @return	Tag
	 */
    public function add($data, $action = 'append')
    {
		if (!(is_scalar($data)  ||
			is_object($data) && is_callable(array($data, '__toString')))) {
			$err  = 'content must be scalar or an object that implements ';
			$err .= '__toString';
			throw new InvalidArgumentException($err);
		}
	
		if (empty($action) || ! is_string($action)) {
			$err = 'action must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$action = strtolower($action);
		switch ($action) {
			case 'append': 
				$this->data[] = (string)$data;
				break;
			case 'prepend':
				array_unshift($this->data, (string)$data);
				break;
			case 'replace':
				$this->data = array((string)$data);
				break;
			default:
				$err  = "you can only append, prepend or replace content ";
				$err .= "the action you gave -($action) is not supported";
				throw new InvalidArgumentException($err);
		}

        return $this;
    }

	/**
	 * @return int
	 */
	public function count()
	{
		return count($this->data);
	}

	/**
	 * @return	bool
	 */
	public function isEmpty()
	{
		return 0 === count($this->data);
	}

	/**
	 * @param	$index  default = null
	 * @return	string|false on failure
	 */
	public function get($index = null)
	{
		if (null === $index) {
			return $this->data;
		}

		if (! is_int($index) || ! isset($this->data[$index])) {
			return false;
		}

		return $this->data[$index];
	}

	/**
	 * @param	int	$index	default null
	 * @return	bool
	 */
	public function clear($index = null)
	{
		if (null === $index) {
			$this->data = array();
			return true;
		}

		if (! is_int($index) || ! isset($this->data[$index])) {
			return false;
		}

		unset($this->data[$index]);
		$this->data = array_values($this->data);
		return true;
	}

	/**
	 * Build the content by concatenating each item in the content array,
	 * use the separator between each item.
	 *
	 * @return string
	 */
	public function build()
	{
		return implode($this->getSeparator(), $this->get());
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->build();
	}
}
