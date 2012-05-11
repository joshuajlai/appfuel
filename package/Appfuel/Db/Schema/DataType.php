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
	Appfuel\Framework\Db\Schema\DataTypeInterface;

/**
 * Vendor agnostic object that decribes a data type. There are two fixed 
 * memebers to the DataType: 
 * 1) name			getName returns vendor specific datatype name
 * 2) typeModifier  getTypeModifier returns whatever is inside the parenth
 * 
 * everything else is obtained through getAttribute 
 */
class DataType extends SchemaObject implements DataTypeInterface
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
	 * @param	mixed	array|DictionaryInterface	$details
	 * @return	DataType
	 */
	public function __construct($details)
	{
		parent::__construct($details);

		$attrList = $this->getAttributeList();
	
		$name = $attrList->get('type-name', null);
		if (empty($name) || ! is_string($name)) {
			throw new Exception("type-name must be a non empty string");
		}
		$this->name = $name;
		$modifier = $attrList->get('type-modifier', null);
		if (! empty($modifier)) {
			$this->typeModifier = $modifier;
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
}
