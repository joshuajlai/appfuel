<?php

class AfIterator implements \Countable, \Iterator
{
    /**
     * Is Read Only
     * Flag to determine if the list element can be changed
     * @var bool
     */
    private $isReadOnly = TRUE;

    /**
     * Count
     * Total number of elements in the list
     * @var int
     */
    private $count = 0;

    /**
     * Data
     * Holds the elements of the list
     * @var array
     */
    private $data = array();

    /**
     * Index
     * Pointer used for iterator implementation
     * @var int
     */
    private $index = 0;

    /**
     * Skip Next 
     * A race condition is caused when unsetting an item in a loop
     * this is used to prevent that from happening
     */
    private $skipIteration = FALSE;

    /**
     * 
     * @param   array   $data       config data
     * @param   bool    $modify     determines if the config can be modified
     * @return  Config
     */
    public function __construct(array $data, $readOnly = TRUE) 
    {
        $this->setReadOnly($readOnly); 
        $this->loadData($data, $readOnly); 
    }

    /**
     * Get
     * Acts as the getter method for data items. Also allows the default
     * return value to be set when data item is not found
     *
     * @param   string  $key        data label 
     * @param   mixed   $default    value returned used when data not found
     * @return  mixed
     */
    public function get($key, $default = NULL)
    {
        if (! $this->exists($key)) {
            return $default;
        }

        return $this->data[$key];
    }

    /**
     * Set
     * Adds an name value pair to the config object
     *
     * @param   string  $name   
     * @param   mixed   $value
     * @return  NULL
     */
    public function set($name, $value)
    {
       $isReadOnly = $this->isReadOnly();
         
        if (TRUE === $isReadOnly) {
            throw new Exception(
                "Label $name is read-only"
            );
        }
    
        $this->loadItem($name, $value, $isReadOnly);
    }

    /**
     * Magic function used for retrieving an object with $obj->name
     * 
     * @param   string  $name
     * @return  mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Modify a label with a given value provided the allowModification flag
     * was toggled in the constructor
     *
     * @param   string  $name   name of the key
     * @param   mixed   $value  value to set
     * @return  void
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * Support isset() overloading on PHP 5.1
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param  string   $name
     * @return void
     */
    public function __unset($name)
    {
        if ($this->isReadOnly()) {
            throw new Exception(
                "Label $name is read-only"
            );
        }

        unset($this->data[$name]);
        $this->setCount(count($this->data));
        $this->setSkipIteration(TRUE);
    }

    /**
     * Clone
     * Ensure deep cloning by iterating though data cloning any
     * any nested Configs
     *
     * @return  void
     */
    public function __clone()
    {
        $result = array();
        $data = $this->data;
        foreach ($data as $key => $value) {
            if ($value instanceof Data) {
                $result[$key] = clone $value;
            } else {
                $result[$key] = $value;
            }
        }

        $this->data = $result;
    }

    /**
     * To Array
     * Converts the list elements to arrays. Allows for deep conversion
     *
     * @return  array
     */
    public function toArray()
    {
        $result = array();
        $data   = $this->data;

        foreach ($data as $key => $value) {
            if ($value instanceof AfIterator) {
                $result[$key] = $value->toArray();
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * To Json
     * Converts the config into a json string
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Set Skip Iteration
     * Flag to tell current to skip iteration
     * 
     * @return void
     */
    protected function setSkipIteration($flag)
    {
        $this->skipIteration =(bool) $flag;
    }

    /**
     * @return bool
     */
    protected function skipIteration()
    {
        return $this->skipIteration;
    }

    /**
     * Set Read Only
     * Flag to determine if the configuration data can be modified.
     * This flag will apply to all nested configs
     *
     * @param   bool $flag
     * @return  Data
     */
    public function setReadOnly($flag)
    {
        $this->isReadOnly =(bool) $flag;
        return $this;
    }

    /**
     * @return  bool
     */
    public function isReadOnly()
    {
        return $this->isReadOnly;
    }
 
    /**
     * Defined by Countable interface
     *
     * @return int
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * Current
     * Iterator implementation that return array item in $_data
     * 
     * @return  mixed
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * Key
     * Iterator implementation that returns the index element of the current 
     * position in $_data
     * 
     * @return mixed
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * Next
     * Iterator implementation that advances the internal array pointer of 
     * $_data and increments index
     *
     * @return void
     */
    public function next()
    {

        /*
         * means a unnset has been performed and
         * we can not call next on an item that 
         * no longer exits so return void
         */
        if ($this->skipIteration()) {
            $this->setSkipIteration(FALSE);
            return NULL;
        }       
        next($this->data);
        $this->index++;
    }

    /**
     * Rewind
     * Iterator implementation that moves the internal array pointer of
     * $_data to the beginning
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->data);
        $this->index = 0;
    }

    /**
     * Valid
     * Iterator implementation that checks if index is in range
     *
     * @return  bool
     */
    public function valid()
    {
        return $this->index < $this->count;
    }

    /**
     * Merge 
     * Combines Config passed in with the existing Config. Keys that exist
     * are written over
     *
     * @param   Data $config
     * @return  Data
     */
    public function merge(AfIterator $config)
    {
        $readOnly = FALSE;
        $value = NULL;
        foreach ($config as $key => $data) {
            
            $isDataConfig = $data instanceof AfIterator;
            
            $value = $data;
            $isMergeNeeded = FALSE;
            if (TRUE === $isDataConfig) {
                $value = $this->create($data->toArray(), $readOnly);
                $isMergeNeeded = $this->exists($key) && 
                                 $this->$key instanceof AfIterator;

                
                if (TRUE === $isMergeNeeded) {
                    $value = $this->$key->merge($value);
                } 
            }
                
            $this->$key = $value;
            
        }

        return $this;
    }

    /**
     * @param   $key    string
     * @return  bool
     */
    public function exists($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @param   array   $data   config data
     * @param   bool    $modify can this data be changed
     * @return  void
     */
    protected function loadData(array $data, $readOnly = TRUE)
    {
        foreach ($data as $key => $value) {
            $this->add($key, $value, $readOnly);
        }

        $this->setCount(count($data));
    }

    /**
     * Load Item
     * Introduces a single configuration item into the data and 
     * updates the counter if that items is new
     *
     * @param   string  $key
     * @param   mixed   $value
     * @param   bool    $readOnly
     * @return  void
     */
    protected function loadItem($key, $value, $readOnly = TRUE)
    {
        /*
         * When item does not exists we have to update the counter
         */
        if (! $this->exists($key)) {
            $numItems = $this->count();
            $this->setCount(++$numItems);
        }

        $this->add($key, $value, $readOnly);
    }   

    /**
     * Add
     * Introduces a new item into the config data. Add does not
     * update the count of config items
     *
     * @param   string  $key        label for config data item
     * @param   mixed   $value      config item
     * @param   bool    $readOnly   determine if this item can change
     * @return  void
     */
    protected function add($key, $value, $readOnly = TRUE)
    {
        
        if (is_array($value)) {
            $this->data[$key] = $this->createConfig($value, $readOnly);
        } else {
            $this->data[$key] = $value;
        }
    }

    /**
     * Create 
     * Factory method used to create a config object
     *
     * @param   array   $data
     * @param   bool    $readOnly     determines if this is read-only
     * @return  Config
     */
    protected function create(array $data, $readOnly = TRUE)
    {
        return new self($data, $readOnly);
    }

    /**
     * @param   int $total
     * @return  void
     */
    protected function setCount($total)
    {
        $this->count = $total;
    }
}
