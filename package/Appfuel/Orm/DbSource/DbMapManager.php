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

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Orm\OrmManager;

/**
 * Facade to all db maps, which allows for top level domains to share each
 * other maps, this is needed to allow for many to many relationships.
 */
class DbMapManager
{
	/**
	 * List of table maps which are cached by domain key, which prevents 
	 * repeated calls to grab the db map.
	 * @var	array
	 */
	static protected $cache = array();

	/**
	 * @param	string	$domain key
	 * @return	array
	 */
	static public function getAllColumns($key, $as = false)
	{
		$members = self::getAllDomainMembers($key);
		$result  = array();
		foreach ($members as $member) {
			$result[] = self::mapColumn($key, $member, $as);
		}
	
		return $result;	
	}

	/**
	 * @param	string	$key
	 * @return	array
	 */
	static public function getAllDomainMembers($key)
	{
		$table = self::getTableMap($key);
		return $table->getAllMembers();
	}

	/**
	 * @param	string	$key
	 * @param	string	$as			strategy for AS 
	 * @param	string	$custom		where $as strategy is custom use this
	 * @return	string
	 */
	static public function mapDomainStr($str, $as = false, $custom = null)
	{
        if (! is_string($str) || false === $pos = strpos($str, '.')) {
			$err = "could not parse string of format -(domain.member)";
			throw new InvalidArgumentException($err);
        }

        $parts  = explode('.', $str);
        $domain = current($parts);
        $member = next($parts);
        return self::mapColumn($domain, $member, $as, $custom);
	}

	/**
	 * @param	string	$domain key
	 * @return	array
	 */
	static public function mapColumn($key, $member, $as = false, $custom = null)
	{
		$table  = self::getTableMap($key);
		$column = $table->mapColumn($member);
		if (false === $column) {
			$err = "map column failed: column not found for -($key, $member)";
			throw new RunTimeException($err);
		}

		$column = "{$table->getTableAlias()}.{$column}";

		switch ($as) {
			case 'member':
				$column .= " AS $member ";
				break;
			case 'qualified':
				$column .= " AS \"$key.$member\"";
				break;
			case 'custom':
				if (! is_string($custom) || empty($custom)) {
					$err = "custom AS must be a non empty string";
					throw new InvalidArgumentException($err);
				}

				$column .= " AS {$custom}";
				break;
		}
	
		return $column;
	}

	static public function getTableReference($key)
	{
		$table = self::getTableMap($key);
		return "{$table->getTableName()} AS {$table->getTableAlias()}";
	}

	/**
	 * @param	string	$domain key
	 * @return	array
	 */
	static public function getTableName($key)
	{
		$table = self::getTableMap($key);
		return $table->getTableName();
	}

    /**
     * @return array
     */
    static public function getAllTableMaps($key)
    {
		$db = self::getDbMap($key);
		return $db->getAllTableMaps();
    }

    /**
     * @param   string  $key
     * @return  DbTableMapInterface
     */
    static public function getTableMap($key)
    {
		$table = self::getFromCache($key);
		if ($table instanceof DbTableMapInterface) {
			return $table;
		}
		
		$db = self::getDbMap($key);
		$table = $db->getTableMap($key);
		if (false === $table) {
			$err = "Db table could not be mapped for -($key)";
			throw new RunTimeException($err);
		}

		self::addToCache($key, $table);
		return $table;
    }

	/**
	 * @throws	RunTimeException
	 * @param	string	$key
	 * @return	DbMapInterface
	 */
	static public function getDbMap($key)
	{
		$db = OrmManager::getDbMap($key);
		if (false === $db) {
			$err = "db map could not be found for -($key)";
			throw new RunTimeException($err);
		}

		return $db;
	}

	/**
	 * @param	string	$key
	 * @return	DbTableMapInterface
	 */
	static protected function getFromCache($key)
	{
        if (! is_string($key) || ! isset(self::$cache[$key])) {
			return false;
		}

        return self::$cache[$key];
	}

	/**
	 * @param	string	$key
	 * @param	DbTableMapInterface	$map
	 * @return	null
	 */
	static protected function addToCache($key, DbTableMapInterface $map)
	{
        if (! is_string($key) || empty($key)) {
            $err = "can not add to cache invalid cache domain key";
            throw new InvalidArgumentException($err);
        }

		self::$cache[$key] = $map;
	}

	/**
	 * @return	array
	 */
	static protected function getCache()
	{
		return self::$cache;
	}
}
