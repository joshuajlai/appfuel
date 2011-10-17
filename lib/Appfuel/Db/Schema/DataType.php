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
namespace Appfuel\Db\Schema;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Schema\DataTypeInterface,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Framework\DataStructure\DictionaryInterface;

/**
 * Vendor agnostic object that decribes a data type. There are two fixed 
 * memebers to the DataType: 
 * 1) name			getName returns vendor specific datatype name
 * 2) typeModifier  getTypeModifier returns whatever is inside the parenth
 * 
 * everything else is obtained through getAttribute 
 */
class DataType implements DataTypeInterface
{
	/**
	 * Name of the datatype
	 * @var	string
	 */
	protected $name = null;

	/**
	 * @var mixed	array|int|null
	 */
	protected $typeModifier = null;

	/**
	 * Name of the column
	 * @var string 
	 */
	protected $attrs = null;

	/**
	 * @return	Column
	 */
	public function __construct($details)
	{
		/*
		 * Allow only associative arrays and dictionary interfaces
		 */
		if (is_array($details) && !(array_values($details) === $details)) {
			$attrs = new Dictionary($details);
		}
		else if ($details instanceof DictionaryInterface) {
			$attrs = $details;
		}
		else {
			$err  = "parameter used in constructor must be an associative ";
			$err .= " or an object that implements Appfuel\Framework";
			$err .= "\DataSource\DictionaryInterface";
			throw new Exception($err);
		}
		
		$name = $attrs->get('type-name', null);
		if (empty($name) || ! is_string($name)) {
			throw new Exception("type-name must be a non empty string");
		}
		$this->name = $name;
		$modifier = $attrs->get('type-modifier', null);
		if (! empty($modifier)) {
			$this->typeModifier = $modifier;
		}
		$this->attrs = $attrs;
	}

	/**
	 * @return	string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Returns the name of the data type
	 *
	 * @return	string
	 */
	public function getTypeModifier()
	{
		return $this->typeModifier;
	}

	/**
	 * @return	bool
	 */
	public function isTypeModifier()
	{
		return ! empty($this->typeModifier);
	}

	/**
	 * @param	string	$name of the attribute
	 * @return	bool
	 */
	public function isAttribute($name) 
	{
		return $this->getAttributeList()
					->exists($name);
	}

	/**
	 * @param	string	$name		name of the attribute
	 * @param	mixed	$default	returned when attr not found
	 * @return	mixed
	 */
	public function getAttribute($name, $default = null)
	{
		return $this->getAttributeList()
					->get($name, $default);
	}

	/**
	 * @return	DictionaryInterface
	 */
	protected function getAttributeList()
	{
		return $this->attrs;
	}
}
