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
class DbMap implements DbMapInterface
{
    /**
     * List of table maps to be injected into the sql template
     * @var array
     */
    protected $maps = array();

	/**
	 * @param	array  $data
	 * @return	DbMap
	 */
	public function __construct(array $maps = null)
	{
        if (null !== $maps) {
            $this->initialize($maps);
        }
	}

	/**
	 * @param	array	$maps
	 * @return	null
	 */
	public function initialize(array $maps)
	{
		if ($maps === array_values($maps)) {
			$err  = 'database map must be an associative array of ';
			$err .= 'key => array of table map data';
			throw new InvalidArgumentException($err);
		}

		foreach ($maps as $key => $table) {
			$map = $this->createTableMap($table);
			$this->addTableMap($key, $map);
		}
	}

	/**
	 * @param	string	$domain key
	 * @return	array
	 */
	public function getAllColumns($key, $isAlias = true, $isDomain = false)
	{
		if (! is_string($key) || ! $this->isTableMap($key)) {
			return false;
		}

		if (true === $isDomain) {
			$columns = $this->getTableMap($key)
							->getAllColumnsAsMembers($key, $isAlias);
		}
		else {
			$columns = $this->getTableMap($key)
							->getAllColumns($isAlias);
		}

		return $columns;
	}

	/**
	 * @param	string	$domain key
	 * @return	array
	 */
	public function mapColumn($key, $member, $isAlias = true, $isDomain = false)
	{
		if (! is_string($key) || ! $this->isTableMap($key)) {
			return false;
		}
			
		$column = $this->getTableMap($key)
					   ->mapColumn($member, $isAlias);

		if (true === $isDomain) {
			$column .= " AS \"$key.$member\"";
		}

		return $column;
	}

	/**
	 * @param	string	$domain key
	 * @return	array
	 */
	public function getTableName($key)
	{
		if (! is_string($key) || ! $this->isTableMap($key)) {
			return false;
		}
			
		$map  = $this->getTableMap($key);
		return array(
			$map->getTableName(),
			$map->getTableAlias()
		);
	}


	/**
	 * @param	array	$data
	 * @return	DbTableMap
	 */
	public function createTableMap(array $data)
	{
		return new DbTableMap($data);
	}

    /**
     * @param   string  $key
     * @return  bool
     */
    public function isTableMap($key)
    {
        if (is_string($key) &&
            isset($this->maps[$key]) &&
            $this->maps[$key] instanceof DbTableMapInterface) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAllTableMaps()
    {
        return $this->maps;
    }

    /**
     * @param   string  $key
     * @return  DbTableMapInterface
     */
    public function getTableMap($key)
    {
        if (! $this->isTableMap($key)) {
            return false;
        }

        return $this->maps[$key];
    }

    /**
     * @param   string  $key
     * @param   DbTableMapInterface $map
     * @return  SqlFileCompositor
     */
    public function addTableMap($key, DbTableMapInterface $map)
    {
        if (! is_string($key) || empty($key)) {
            $err = 'table map key must be a non empty string';
            throw new InvalidArgumentException($err);
        }

        $this->maps[$key] = $map;
        return $this;
    }

    /**
     * @param   array   $list 
     * @return  SqlCompositor
     */
    public function loadTableMaps(array $list)
    {
        foreach ($list as $key => $map) {
            $this->addTableMap($key, $map);
        }

        return $this;
    }

    /**
     * @param   array   $list 
     * @return  SqlCompositor
     */
    public function setTableMaps(array $list)
    {
        $this->clearTableMaps();
        $this->loadTableMaps($list);
        return $this;
    }

    /**
     * @return  SqlFileCompositor
     */
    public function clearTableMaps()
    {
        $this->maps = array();
        return $this;
    }
}
