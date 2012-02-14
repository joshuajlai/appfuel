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
namespace Appfuel\DataStructure;

/**
 * A dictionary is an unordered collection of zero or more elements of some 
 * type. This basically hides the need to have to use array_key_exists to
 * prevent undefined indexes and allows for default values on a get method.
 */
class Dictionary implements DictionaryInterface
{
	/**
	 * Items stored in the bag
	 * @var array
	 */
	protected $items = array();

	/**
	 * @param	array	$data
	 * @return	Bag
	 */
	public function __construct(array $data = null)
	{
		if (null === $data) {
			$data = array();
		}

		$this->load($data);
	}

	/**
	 * @return int
	 */
	public function count()
	{
		return count($this->items);
	}

	/**
	 * @param	string	$key	item key
	 * @param	mixed	$value	
	 * @return	Bag
	 */	
	public function add($key, $value)
	{
		if (! is_scalar($key)) {
			return $this;
		}

		$this->items[$key] = $value;
		return $this;
	}

	/**
	 * @param	string	$key
	 * @param	mixed	$default		return value when not found
	 * @return	mixed
	 */
	public function get($key, $default = NULL)
	{
		if (! array_key_exists($key, $this->items)) {
			return $default;
		}

		return $this->items[$key];
	}

	/**
	 * @param	string $key
	 * @return	bool
	 */
	public function exists($key)
	{
		return array_key_exists($key, $this->items);
	}

    /**
     * Answers the question does this exist and is it a particular type
     *
     * @param   string  $key
     * @param   mixed string|object  $type   type that thing should be
     * @return  bool
     */
    public function existsAs($key, $type)
    {   
        if (empty($type) || ! $this->exists($key)) {
            return false;
        }

        $item = $this->items[$key];

        switch ($type) {
            case 'array'   : $isType  = is_array($item);    break;
            case 'bool'    : $isType  = is_bool($item);     break;
            case 'float'   : $isType  = is_float($item);    break;
            case 'int'     : $isType  = is_int($item);      break;
            case 'numeric' : $isType  = is_numeric($item);  break;
            case 'scalar'  : $isType  = is_scalar($item);   break;
            case 'object'  : $isType  = is_object($item);   break;
            case 'string'  : $isType  = is_string($item);   break;
            case 'resource': $isType  = is_resource($item); break;
            case 'callable': $isType  = is_callable($item); break;
            case 'null'    : $isType  = is_null($item);     break;
			case 'empty'   : $isType  = empty($item);		break;
			case 'bool-true'  : $isType  = $item === true;	break;
			case 'bool-false' : $isType  = $item === false;	break;
			case 'non-empty-string': 
				$isType = ! empty($item) && is_string($item); 
				break;
            default        : 
				$isType  = $item instanceof $type;
        }

        return $isType;
    }

	public function merge($data)
	{
		if ($data instanceof DictionaryInterface) {
			$data = $data->getAll();
		}
		else if (! is_array($data)) {
			$err  = 'dictionary can only merge an array or another Appfuel';
			$err .= 'DataStructure\DictionaryInterface';
			throw new InvalidArgumentException($err);
		}

		foreach ($data as $key => $value) {
			if (! $this->exists($key)) {
				$this->add($key, $value);
			}
			
			$item = $this->data[$key];
			if ($item instanceof DictionaryInterface) {
				if (is_array($value) && $value instanceof DictionaryInterface){
					$item->merge($value);
				}
				else {
					$this->add($key, $value);
				}
				continue;
			}
		
			if (! is_array($item) || ! is_array($value)) {
				$this->add($key, $value);
				continue;
			}

			/* item is an array and value is not */
			$this->arrayMerge($value, $item);
		}

	}

	protected function arrayMerge(array $merge, array $src)
	{
		foreach ($merge as $key => $value) {
			if (! array_key_exists($key, $src)) {
				$src[$key] = $value;
				continue;
			}

			$item = $src[$key];
			if (is_array($value) && is_array($item)) {

				/* merge the unique values togather for indexed arrays*/
				if ($value === array_values($value)) {
					foreach ($value as $arrItem) {
						$src[$key][] = $arraItem;
					}
					$src[$key] = array_unique($src[$key]);
				}
				/* recurse through assosiative arrays */
				else {
					$src[$key] = $this->arrayMerge($value, $item);
				}
			}
			else {
				$src[$key] = $value;
				continue;
			}
		}
	}

	/**
	 * @param	string	$key
	 * @param	type	$type	name of class to test for
	 * @return	bool
	 */
	public function exitsAsObject($key, $type)
	{
	    if (! $this->exists($key) || ! is_string($type)) {
            return false;
        }

		return $this->items[$key] instanceof $type;
	}

	/**
	 * Return the entire dataset
	 * 
	 * @return array
	 */
	public function getAll()
	{
		return $this->items;
	}

	/**
	 * @param	array	$data
	 * @return	Bag
	 */
	public function load(array $data)
	{
		foreach ($data as $key => $value) {
			$this->add($key, $value);
		}

		return $this;
	}
}

