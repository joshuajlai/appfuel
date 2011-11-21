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
namespace Appfuel\Acl;

use InvalidArgumentException;

/**
 * The acl role is an immutable value object that defines an authority level
 */
class AclRole implements AclRoleInterface
{
	/**
	 * Name or label you would see in the user interface
	 * @var string
	 */
	protected $name = null;

	/**
	 * Code used in white and black list to determine access
	 * @var	bool
	 */
	protected $code = null;

	/**
	 * Used to compare against other roles to determine hierarchy
	 * @var int
	 */
	protected $priority = null;

	/**
	 * Description shown in the ui
	 * @var string
	 */
	protected $description = null;

	/**
	 * @param	string	$name
	 * @param	string	$code
	 * @param	int		$level
	 * @param	string	$description
	 * @return	AclRole
	 */
	public function __construct($name, $code, $priority, $description = null)
	{
		$this->setName($name);
		$this->setCode($code);
		$this->setPriority($priority);

		if (null !== $description) {
			$this->setDescription($description);
		}
	}

	/**
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return	string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @return	int
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setName($name)
	{
		if (empty($name) || ! is_string($name)) {
			throw new InvalidArgumentException(
				'name must be a none empty string'
			);
		}
		$this->name = $name;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setCode($name)
	{
		if (empty($name) || ! is_string($name)) {
			throw new InvalidArgumentException(
				'name must be a none empty string'
			);
		}
		$this->code = $name;
	}

	/**
	 * @param	int		$level
	 * @return	null
	 */
	protected function setPriority($level)
	{
		if (! is_int($level)) {
			throw new InvalidArgumentException(
				'level must be a number'
			);
		}
		$this->priority = $level;
	}

	/**
	 * @param	string	$text
	 * @return	null
	 */
	protected function setDescription($text)
	{
		if (empty($text) || ! is_string($text)) {
			throw new InvalidArgumentException(
				'description must be a string'
			);
		}
		
		$this->description = $text;
	}
}
