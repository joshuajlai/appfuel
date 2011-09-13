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
namespace Appfuel\View;

use Appfuel\Framework\Exception,
	Appfuel\Framework\View\TemplateInterface,
	Appfuel\Framework\View\ViewFormatterInterface;

/**
 * The view template is the most basic of the templates. Holding all its data
 * in key/value pair it uses a formatter to convert it a string.
 */
class ViewTemplate implements TemplateInterface
{
	/**
	 * Holds assignment until build time where they are passed into scope
	 * @var array
	 */
	protected $assign = array();

	/**
	 * @param	mixed	$file 
	 * @param	array	$data
	 * @return	FileTemplate
	 */
	public function __construct(array $data = null, 
								ViewFormatterInterfaace $formatter = null)
	{
		if (null !== $data) {
			$this->load($data);
		}
	}

	/**
	 * @param	array	$data
	 * @return	ViewTemplate
	 */
	public function load(array $data)
	{
		foreach ($data as $key => $value) {
			$this->assign($key, $value);
		}

		return $this;
	}

	/**
	 * Assign key value pair into the template. This assignment will not reach
	 * the templates scope until the build method has been used to convert it
	 * into a string.
	 *
	 * @param	scalar	$key
	 * @param	mixed	$value
	 * @return	ViewTemplate
	 */
	public function assign($key, $value)
	{
        if (empty($key) || ! is_scalar($key)) {
			throw new Exception("Template assignment keys must be scalar ");
        }

        $this->assign[$key] = $value;
		return $this;
	}

	/**
	 * @param	string	$key
	 * @return	mixed | default on failure
	 */
	public function getAssigned($key, $default = null)
	{	
		if (! $this->isAssigned($key)) {
			return $default;
		}

		return $this->assign[$key];
	}

	/**
	 * @param	string
	 * @return	bool
	 */
	public function isAssigned($key)
	{
		if (empty($key) || ! is_scalar($key) || ! isset($this->assign[$key])) {
			return false;
		}

		return true;
	}

	/**
	 * @return	array
	 */
	public function getAllAssigned()
	{
		return $this->assign;
	}

	/**
	 * Build the template file indicated by key into string. Use data in
	 * the dictionary as scope
	 *
	 * @param	string	$key	template file identifier
	 * @param	array	$data	used for private scope
	 * @return	string
	 */
    public function build(array $data = null, $isPrivate = false)
	{
		$isPrivate = (bool) $isPrivate;
		$formatter = $this->getFormatter();
	
		/*
		 * When private use only data in the second parameter. 
		 * When not private and data in second parameter then merge
		 * When not private and no data then use only data in dictionary
		 */
		if (true === $isPrivate) {
			return $formatter->format($data);
		}
		else if (! empty($data)) {
			$data = array_merge($this->getAllAssigned(), $data);
		}
		else {
			$data = $this->getAllAssigned();
		}
	
		return $formatter->format($data);
	}

	/**
	 * @return	string
	 */
	public function __toString()
	{
		$result = $this->build();
	}
}
