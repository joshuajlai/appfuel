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
namespace Appfuel\Db\Mysql\Constraint;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Constraint\ConstraintKeyInterface;

/**
 * Constaint used in tables to limit a column or columns to unique values
 */
class Key extends AbstractConstraint implements ConstraintKeyInterface
{
	/**
	 * Key constraints are both index and constraint.
	 * @var string
	 */
	protected $indexName = null;

    /**
     * List of columns that this constaint controls
     * @var array
     */
    protected $columns = array();

	/**
	 * @return	DefaultValue
	 */
	public function __construct($name, $columns, $isUnique = false) 
	{
		if (! empty($name)) {
			$this->setIndexName($name);
		}

		if (is_string($columns)) {
			$this->addColumn($columns);	
		} 
		else if (is_array($columns)) {
			foreach ($columns as $column) {
				$this->addColumn($column);
			}
		}
		else {
			$err = "columns must be a string or an array of strings";
			throw new Exception($err); 
		}
		
		$sqlPhrase = 'key';
		if (true === $isUnique) {
			$sqlPhrase = 'unique key';
		}
		parent::__construct($sqlPhrase);
	}

	/**
	 * @return	string
	 */
	public function getIndexName()
	{
		return $this->indexName;
	}

	/**
	 * @return	string
	 */
	public function buildSql()
	{
		$indexName = $this->getIndexName(); 
		$sqlValue  = "({$this->buildColumnString()})";
		if (! empty($indexName)) {
			$sqlValue = "$indexName $sqlValue";
		}

		$sqlValue = strtolower($sqlValue);
		if ($this->isUpperCase()) {
			$sqlValue = strtoupper($sqlValue);
		}

		return parent::buildSql() . " $sqlValue";
	}

    /**
     * @return  array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param   string  $name   name of the column
     * @return  AbstractConstraint
     */
    protected function addColumn($name)
    {
        if (empty($name) || ! is_string($name)) {
            throw new Exception("column name must be a non empty string");
        }

        if (in_array($name, $this->columns)) {
            return $this;
        }

        $this->columns[] = $name;
        return $this;
    }

	/**
	 * Turns an array of columns into a comma delimited string
	 *
	 * @return	string
	 */
    protected function buildColumnString()
    {
        if (empty($this->columns)) {
            return '';
        }

        return implode(',', $this->columns);
    }

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setIndexName($name)
	{
		if (! is_string($name)) {
			throw new Exception("index name must be string");
		}
		$this->indexName = $name;
	}
}
