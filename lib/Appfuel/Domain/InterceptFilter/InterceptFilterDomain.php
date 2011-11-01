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
namespace Appfuel\Domain\InterceptFilter;

use Appfuel\Framework\Exception,
	Appfuel\Orm\Domain\DomainModel,
	Appfuel\Framework\Domain\InterceptFilter\InterceptFilterDomainInterface;

/**
 * An intercepting filter filter is used by the front controller to apply
 * business logic before (pre filters) or after (post filters) the action
 * controller has been executed. It is key to note that the domain is not
 * the actual Intercepting filter, but an abtraction holding the details of
 * a filter. 
 */
class InterceptFilterDomain 
	extends DomainModel implements InterceptFilterDomainInterface
{
	/**
	 * Unqiue string given to identify the filter
	 * @var string
	 */
	protected $key = null;

	/**
	 * Filters belong to one of two types pre or post
	 * @var string
	 */
	protected $type = null;

	/**
	 * Text used to describe this filter
	 * @var string
	 */
	protected $description = null;

	/**
	 * @return	string
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @param	string	$name
	 * @return	InterceptFilterDomain
	 */
	public function setKey($key)
	{
		if (! $this->isNonEmptyString($key)) {
			throw new Exception("Name must be a non empty string");
		}
		$this->key = $key;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param	string	$type
	 * @return	InterceptFilterDomain
	 */
	public function setType($type)
	{
		if (empty($type) || ! is_string($type)) {
			throw new Exception("type must be a non empty string");
		}
		
		$type = strtolower($type);
		if ('post' !== $type && 'pre' !== $type) {
			throw new Exception("intercepting filter can only be -(pre|post)");
		}

		$this->type = $type;
		$this->_markDirty('type');
		return $this;
	}

	/**
	 * @return	string	
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param	string	$text
	 * @return	InterceptingFilterDomain
	 */
	public function setDescription($text)
	{
		if (! is_string($text)) {
			throw new Exception("description must be a string");
		}

		$this->description = $text;
		return $this;
	}
}
