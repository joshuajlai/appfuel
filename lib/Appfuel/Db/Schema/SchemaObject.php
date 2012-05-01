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
	Appfuel\Framework\Db\Schema\SchemaObjectInterface,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Framework\DataStructure\DictionaryInterface;

/**
 * A generic representation of schema object with a list attributes
 */
class SchemaObject implements SchemaObjectInterface
{
	/**
	 * Name of the column
	 * @var string 
	 */
	protected $attrs = null;

	/**
	 * @param	name
	 * @return	SchemaObject
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
		$this->attrs = $attrs;
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
