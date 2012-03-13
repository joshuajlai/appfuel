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
namespace Appfuel\Orm\DbSource;

use InvalidArgumentException;

/**
 * The orm map is used to map database
 */
interface DbMapInterface
{
	/**
	 * @param	array	$maps
	 * @return	null
	 */
	public function initialize(array $maps);

	/**
	 * @param	array	$data
	 * @return	DbTableMap
	 */
	public function createTableMap(array $data);

    /**
     * @param   string  $key
     * @return  bool
     */
    public function isTableMap($key);

    /**
     * @return array
     */
    public function getAllTableMaps();

    /**
     * @param   string  $key
     * @return  DbTableMapInterface
     */
    public function getTableMap($key);

    /**
     * @param   string  $key
     * @param   DbTableMapInterface $map
     * @return  SqlFileCompositor
     */
    public function addTableMap($key, DbTableMapInterface $map);

    /**
     * @param   array   $list 
     * @return  SqlCompositor
     */
    public function loadTableMaps(array $list);

    /**
     * @param   array   $list 
     * @return  SqlCompositor
     */
    public function setTableMaps(array $list);

    /**
     * @return  SqlFileCompositor
     */
    public function clearTableMaps();
}
