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
namespace Appfuel\Framework\Orm\Domain;

/**
 * The domain identity is responsible for mapping datasource properity names
 * to the corresponding domain.
 */
interface DbDomainIdentityInterface
{
    /**
     * @return  array
     */
	public function getMap();
	
    /**
	 * The map is a domainMember=>column_name map
	 *
     * @param   array   $map
     * @return  DbIdentityInterface
     */
	public function setMap(array $map);
	
	/**
     * returns a list of all columns for this domain 
     * 
     * @return array
     */
	public function getAllColumns();
	
    /**
     * return the column for this domain member given or false if no column
     * is mapped
     *
     * @param   string  $columnName
     * @return  string  | false on failure
     */
	public function mapToColumn($memberName);
	
	/**
	 * @param	string	$columnName
	 * @return	bool
	 */
	public function isColumn($columnName);

    /**
     * reuturn the member name mapped for this column
     *
     * @param   string  $columnName
     * @return  string | false on failure
     */
    public function mapToMember($columnName);

    /**
     * @param   string  $memberName
     * @return  bool
     */
    public function isMember($memberName);

    /**
     * returns a list of all domain members for this domin
     * 
     * @return  array
     */
    public function getAllMembers();
    
	/**
     * @return string
     */
	public function getTable();
	
    /**
	 * @param	string	$name
     * @return	DbIntentityInterface
     */
	public function setTable($name);

	/**
	 * @return array
	 */
	public function getPrimaryKey();
	
	/**
	 * Holds a list of column names that represent the primary keys of 
	 * the table this domain belongs to
	 *
	 * @param	array	$key
	 * @return	DbIntentityInterface
	 */
	public function setPrimaryKey(array $key);

	/**
	 * @param	string	
	 */
	public function getDomainName();
	
	/**
	 * The label is used to determine how the domain is referenced in code.
	 * When used with factories for repos you use this key
	 *
	 * @param	string	$label
	 * @return	DbIntentityInterface
	 */
	public function setDomainName($label);
	
	/**
	 * @return array
	 */
	public function getDependencies();
	
    /**
     * @return  DbIdentity
     */
	public function setDependencies(array $list);
}
