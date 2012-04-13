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
namespace Appfuel\Orm\DbSource\Sql;

use LogicException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\View\FileTemplate,
	Appfuel\Expr\ExprListInterface,
	Appfuel\Orm\DbSource\DbMapManager;

/**
 * Builds an sql string from an array specification.
 */
class SqlSelectBuilder
{
	/**
	 * @var string
	 */
	protected $tpl = 'appfuel/sql/mysql/select.psql';

	/**
	 * Used to store data before sql is assigned
	 * @var array
	 */
	protected $data = array(
		'keywords'	=> array(),
		'columns'	=> array(),
		'from'		=> null,
		'joins'		=> array(),
		'where'		=> null,
		'group'		=> null,
		'having'	=> null,
		'order'		=> null,
		'limit'		=> null,
	);

	/**
	 * List of values used where isPrepared is true
	 * @var array
	 */
	protected $values = array(
		'columns' => array(),
		'joins'   => array(),
		'where'   => array(),
		'group'   => array(),
		'having'  => array(),
		'order'   => array(),
		'limit'   => array(),
	);

	/**
	 * @param	string	$tplFile
	 * @return	SqlSelectBuilder
	 */
	public function __construct($tplFile = null)
	{
		if (null !== $tplFile) {
			$this->setTplPath($tplFile);
		}
	}

	/**
	 * @param	array	$spec
	 * @return	string
	 */	
	public function build(array $spec, $isPrepared = true)
	{
		$isPrepared = ($isPrepared === false) ? false : true;
		if (isset($spec['keywords'])) {
			$this->addKeywords($spec['keywords']);
		}

		if (isset($spec['domain-columns'])) {
			$this->addDomainColumns($spec['domain-columns']);
		}

		if (isset($spec['db-columns'])) {
			$this->addDbColumns($spec['db-columns']);
		}

		if (isset($spec['domain-from'])) {
			$this->setDomainFrom($spec['domain-from']);
		}
		else if (isset($spec['db-from'])) {
			$this->setDbFrom($spec['db-from']);
		}

		if (isset($spec['domain-joins'])) {
			$this->loadDomainJoins($spec['domain-joins']);
		}

		if (isset($spec['db-joins'])) {
			$this->loadDomainJoins($spec['db-joins']);
		}

		if (isset($spec['domain-where'])) {
			$this->loadDomainWhere($spec['domain-where'], $isPrepared);
		}

		if (isset($spec['domain-search'])) {
			$this->loadDomainSearch($spec['domain-search'], $isPrepared);
		}

		if (isset($spec['domain-group'])) {
			$this->loadDomainGroupBy($spec['domain-group'], $isPrepared);
		}
			
		if (isset($spec['db-group'])) {
			$this->loadDbGroupBy($spec['db-group']);
		}

		if (isset($spec['domain-order'])) {
			$this->loadDomainOrderBy($spec['domain-order']);
		}

		if (isset($spec['db-order'])) {
			$this->loadDbOrderBy($spec['db-order']);
		}

		if (isset($spec['limit'])) {
			$limit = $spec['limit'];
			if (is_array($limit)) {
				$offset = current($limit);
				$max    = next($limit);

				/* when max is not set make sure its a null */
				if (empty($max)) {
					$max = null;
				}
			}
			$this->setLimit($offset, $max, $isPrepared);
		}		

		$template = $this->createTemplate($this->getTplPath());
		if (is_array($this->data['keywords'])) {
			$keywords = implode(' ', $this->data['keywords']);
			$template->assign('keywords', $keywords);
		}

		if (is_array($this->data['columns'])) {
			$columns = implode(', ', $this->data['columns']);
			$template->assign('columns', $columns);
		}
	
		if (is_string($this->data['from'])) {
			$template->assign('from', $this->data['from']);
		}

		if (is_array($this->data['joins'])) {
			$joins = implode(PHP_EOL, $this->data['joins']);
			$template->assign('joins', $joins);
		}
	
		if (is_array($this->data['where'])) {
			$where = implode(' AND ', $this->data['where']);
			$template->assign('where', $where);
		}

		if (is_array($this->data['group'])) {
			$group = implode(', ', $this->data['group']);
			$template->assign('group', $group);
		}
		
		if (is_array($this->data['order'])) {
			$order = implode(', ', $this->data['order']);
			$template->assign('order', $order);
		}

		if (is_string($this->data['limit'])) {
			$template->assign('limit', $this->data['limit']);
		}
		
		$result = $template->build();
		if (true === $isPrepared) {
			$result = array($result, $this->getPreparedValues());
		}
		
		return $result;
	}

	public function getTplPath()
	{
		return $this->tpl;
	}

	public function createTemplate($tpl)
	{
		return new FileTemplate($tpl);
	}

	
	/**
	 * @param	scalar	$value
	 * @return	SqlHelper
	 */
	public function addValue($type, $value)
	{
		if (! is_array($value) && ! is_scalar($value)) {
			$err = "db value must be a scalar or array value -($type) given";
			throw new InvalidArgumentException($err);
		}

		if (! isset($this->values[$type])) {
			$typeList = implode('| ', array_keys($this->values));
			$err = "type must be a one of the following strings -($typeList)";
			throw new InvalidArgumentException($err);
		}
	
		if (is_array($value)) {
			$this->values[$type] = array_merge($this->values[$type], $value);
			return $this;
		}

		$this->values[$type][] = $value;
		return $this;
	}

	/**
	 * @return	array
	 */
	public function getValues($type = null)
	{
		if (null === $type) {
			return $this->values;
		}

		if (! is_string($type) || ! isset($this->values[$type])) {
			return false;
		}
	
		return $this->values[$type];
	}

	/**
	 * @return	array
	 */
	public function getPreparedValues()
	{
		return array_merge(
			$this->values['columns'],
			$this->values['joins'],
			$this->values['where'],
			$this->values['group'],
			$this->values['having'],
			$this->values['limit']
		);
	}

	/**
	 * @param	array	$list
	 * @return	SqlSelectBuilder
	 */
	public function addKeywords(array $list)
	{
		$result = array();
		foreach ($list as $keyword) {
			if (! is_string($keyword) || empty($keyword)) {
				continue;
			}
			$result[] = $keyword;
		}

		$this->data['keywords'][] = implode(' ', $result);
		return $this;
	}

	/**
	 * @param	array			$list	list of domains members to be mapped
	 * @param	DbMapInterface	$map	db column to domain member map
	 * @param	bool			$isAlias 
	 * @param	bool			$isMapBack	will use the domain member name
	 *							in the columns 'AS' this creates a natural 
	 *							map back to the same domain keys
	 * @return	MysqlSelectBuilder
	 */
	public function addDomainColumns($spec)
	{
		if (is_string($spec)) {
			$spec  = strtolower($spec);
			$parts = explode(',', $spec, 2);
			$key   = trim(current($parts));
			$asKey = trim(next($parts));
 
			$as = false;
			if ('all-as-member' === $asKey) {
				$as = 'member';
			}
			else if ('all-as-qualified' === $asKey) {
				$as = 'qualified';
			}

			$columns = DbMapManager::getAllColumns($key, $as);
			$this->data['columns'][] = implode(',', $columns);
			return $this;
		}
		else if (! is_array($spec)) {
			$err  = "failed to add domain columns: specification must be a ";
			$err .= "string or an array ";
			throw new InvalidArgumentException($err);
		}

		$columns  = array();
		foreach ($spec as $key => $data) {
			if (! is_string($key) || empty($key)) {
				$err = 'domain key must be a non empty string';
				throw new InvalidArgumentException($err);
			}
			if (is_string($data)) {
				$all = strtolower($data);
				$as  = false;
				if ('all-as-member' === $all) {
					$as = 'member';
				}
				else if ('all-as-qualified' === $all) {
					$as = 'qualified';
				}

				$columns = array_merge(
					$columns, 
					DbMapManager::getAllColumns($key, $as)
				);
			}
			else if (is_array($data)) {
				foreach ($data as $memberStr) {
					if (! is_string($memberStr)) {
						$err  = "failed to add domain columns: domain member ";
						$err .= "list must be a list of strings";
						throw new InvalidArgumentException($err);
					}
					$parts    = explode(',', $memberStr, 3);
					$member   = current($parts);
					$asStr    = next($parts);
					$asCustom = next($parts);
					
					$as = false;
					if (is_string($asStr) && ! empty($asStr)) {
						$as = trim($asStr);
					}

					$custom = null;
					if (is_string($asCustom) && ! empty($asCustom)) {
						$custom = trim($asCustom);
					}					
					$columns[] = DbMapManager::mapColumn(
						$key, 
						trim($member), 
						$as,
						$custom
					);
				}
			}
		}

		$this->data['columns'][] = implode(', ', $columns);
		return $this;
	}

	/**
	 * @param	array	$list
	 * @param	DbMapInterface	$map
	 * @param	bool	$isAlias
	 * @return	MysqlSelectBuilder
	 */
	public function addDbColumns(array $list)
	{
		$columns = array();
		foreach ($list as $index => $spec) {
			if (is_string($spec)) {
				$columns[] = $spec;
			}
			else if (is_array($spec) && 3 === count($spec)) {
				$str       = $spec[0];
				$marker    = $spec[1];
				$domainStr = $spec[2];
				if (! is_string($marker) || false === strpos($str, $marker)) {
					$err  = "marker to replace is not a string or is ";
					$err .= "not found in -($str)";
					throw new RunTimeException($err);
				}
				$column = DbMapManager::mapDomainStr($domainStr, false);
				$columns[] = str_replace($marker, $column, $str);
			}
		}

		$this->data['columns'][] = implode(', ', $columns);
		return $this;
	}

	/**
	 * @param	string	$key
	 * @param	DbMapInterface $map
	 * @param	bool	$isAlias
	 * @return	MysqlSelectBuilder
	 */
	public function setDomainFrom($key)
	{
		$this->data['from'] = DbMapManager::getTableReference($key);
		return $this;
	}

	/**
	 * @param	string
	 * @return	MysqlSelectBuilder
	 */
	public function setDbFrom($table)
	{
		if (! is_string($table) || empty($table)) {
			$err = 'failed to set from: table must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->data['from'] = $table;
		return $this;
	}
	
	/**
	 * @param	string	$domainStr
	 * @param	DbMapInterface $map
	 * @return	MysqlSelectBuilder
	 */
	public function mapJoinColumn($domainStr, $key)
	{
		if (! is_string($domainStr) || empty($domainStr)) {
			$err = 'domain to column map failed: must be a non empty string';
			throw new InvalidArgumentException($err);
		}
		$isAlias   = true;
		if (false !== $pos = strpos($domainStr, '.')) {
			$isMapBack = false;
			$column = DbMapManager::mapDomainStr($domainStr);
		}
		else {
			$column = DbMapManager::mapColumn($key, $domainStr);
		}

		return $column;
	}
	
	/**
	 * @param	array			$list
	 * @param	DbMapInterface	$map
	 * @return	MysqlSelectBuilder
	 */
	public function loadDomainJoins(array $list)
	{
		$joinType = 'inner';
		foreach ($list as $key => $data) {
			$stmt = 'LEFT JOIN ';
			$onOp = '=';
			$tableName = DbMapManager::getTableReference($key);
			if (is_string($data['type'])) {
				$joinType = strtolower($data['type']);
			}
			if ('inner' === $joinType) {
				$stmt = 'INNER JOIN ';	
			}
			$stmt .= DbMapManager::getTableReference($key) . ' ON ';
			
			if (! isset($data['left']) || ! is_string($data['left'])) {
				$err  = "could not load join -($key) key for left column ";
				$err .= " -(left) is not set or is not a string";
				throw new InvalidArgumentException($err);
			}
			
			$leftColumn = $this->mapJoinColumn($data['left'], $key);

			if (! isset($data['right']) || ! is_string($data['right'])) {
				$err  = "could not load join -($key) key for right column ";
				$err .= "-(right) is not set or is not a string";
				throw new InvalidArgumentException($err);
			}
			$rightColumn = $this->mapJoinColumn($data['right'], $key);

			if (isset($data['on-op']) && is_string($data['on-op'])) {
				$onOp = $data['on-op'];
			}
			$this->data['joins'][] = "$stmt $leftColumn $onOp $rightColumn";
		}

		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	MysqlSelectBuilder
	 */
	public function loadDbJoins(array $list)
	{
		foreach ($list as $join) {
			if (! is_string($join) || empty($join)) {
				$err = "manual join must be a non empty string";
				throw new InvalidArgumentException($err);
			}
			$this->data['joins'][] = $joins;
		}
		
		return $this;
	}

	/**
	 * @param	ExprListInterface	$list
	 * @param	DbMapInterface		$map
	 * @return	MysqlSelectBuilder
	 */
	public function loadDomainWhere(ExprListInterface $list, $isPrepared = true)
	{
		$this->data['where'][] = $this->processExprList(
			'where', 
			$list, 
			$isPrepared
		);
		return $this;
	}

	public function loadDomainSearch(array $spec, $isPrepared)
	{
		if (! isset($spec['term']) || ! is_string($spec['term'])) {
			$err  = "search term must be defined with key -(term) and must ";
			$err .= "be a non empty string";
			throw new InvalidArgumentException($err);
		}
		$term = trim($spec['term']);
		if (empty($term)) {
			return $this;
		}
		$termList = explode(' ', $term, 20);
	
		$terms = array();
		$termsIn = '';
		foreach ($termList as $term) {
			$terms[] = $term;
			if (true === $isPrepared) {
				$termsIn .= '?,';
			}
			else {
				$termsIn .= "\"$term\",";
			}
		}
		$termsIn = trim($termsIn, ",");	
		if (! isset($spec['members']) || ! is_array($spec['members'])) {
			$err  = "search domain members must be an array of fully  ";
			$err .= "qualified domain members";
			throw new InvalidArgumentException($err);
		}

		$targetList = $spec['members'];
		$result = array();
		foreach ($targetList as $domainStr) {
			$column = DbMapManager::mapDomainStr($domainStr);
			$str = '';
			foreach ($terms as $term) {
				if (true === $isPrepared) {
					$str .= "($column LIKE ?) OR ";
					$this->addValue('where', "%$term%");
				}
				else {
					$str .= "($column LIKE \"%$term%\") OR ";

				}
			}
			$str .= "($column IN($termsIn)) OR";
			if (true === $isPrepared) {
				$this->addValue('where', $terms);
			}
			$result[] = trim($str, "OR");
		}

		$expr = ''; 
		if (! empty($this->data['where'])) {
			$expr = ' AND ';
		}
		$expr .= ' (' . implode(' OR ', $result) . ')';
		$this->data['where'][] = $expr;
		return $this;
	}

	/**
	 * @param	array	$list	list of domain members to group by
	 * @param	DbMapInterface $map
	 * @param	bool	$isAlias
	 * @return	MysqlSelectBuilder
	 */
	public function loadDomainGroupBy(array $list)
	{
		$result = array();
		foreach ($list as $spec) {
			if (is_string($spec)) {
				$result[] = DbMapManager::mapDomainStr($spec);
			}
		}

		$this->data['group'][] = implode(', ', $result);
	}

	/**
	 * @param	array	$list
	 * @return	MysqlSelectBuilder
	 */
	public function loadDbGroupBy(array $list)
	{
		$this->data['group'][] = implode(', ', $list);
		return $this;
	}

	/**
	 * @param	array	$list
	 * @param	DbMapInterface	$map
	 * @param	bool	$isAlias
	 * @return	MysqlSelectBuilder
	 */
	public function loadDomainOrderBy(array $list)
	{
		$result = array();
		$dir    = 'asc';
		foreach ($list as $spec) {
			if (is_string($spec)) {
				$result[] = $spec;
			}
			else if (is_array($spec)) {
				$domainStr = current($spec);
				$tmp       = next($spec);
				if (is_string($dir)) {
					$tmp = strtolower($tmp);
					if ('asc' === $tmp || 'desc' === $tmp) {
						$dir = $tmp;
					}
				}
				$column = DbMapManager::mapDomainStr($domainStr);
				$result[] = "$column $dir";
			}
		}

		$this->data['order'][] = implode(', ', $result);
		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	MysqlSelectBuilder
	 */
	public function loadDbOrderBy(array $list)
	{
		$this->data['order'][] = implode(',', $list);
		return $this;
	}

	/**
	 * @param	int		$offset
	 * @param	int		$max
	 * @param	bool	$isPrepared
	 * @return	MysqlSelectBuilder
	 */
	public function setLimit($offset, $max = null, $isPrepared = true)
	{
        if (! is_int($offset) || $offset < 0)  {
            $err = "set limit failed: 1st param must be an positive int";
            throw new InvalidArgumentException($err);
        }

		if (null === $max) {
			$limit = $offset;
			if (true === $isPrepared) {
				$limit = '?';
				$this->addValue('limit', $offset);
			}
			$this->data['limit'] = $limit;
			return $this;
		}

        if (! is_int($max) || $max < 0)  {
            $err = "set limit failed: 2nd param must be an positive int";
            throw new InvalidArgumentException($err);
        }
	
		$limit = "$offset, $max";	
		if (true === $isPrepared) {
			$limit = "?, ?";
			$this->addValue('limit', array($offset, $max));
		}

		$this->data['limit'] = $limit;
		return $this;
	}

	/**
	 * @param	string	$type
	 * @param	ExprListInterface $list
	 * @param	DbMapInterface $map
	 * @return	MysqlSelectBuilder
	 */
	public function processExprList($type, 
									ExprListInterface $list, 
									$isPrepared = true)
	{
		$result = '';
        foreach ($list as $key => $data) {
            $expr = current($data);
            $column = DbMapManager::mapColumn(
						$expr->getDomain(), 
						$expr->getMember());

            if (true === $isPrepared) {
                $this->addValue($type, $expr->getValue());
				$value = '?';
            }
			else {
				$value = $expr->getValue();
			}

            $op = $expr->getOperator();
            $result .= "{$column} {$op} {$value} ";
            $relOp = next($data);
            if ('and' === $relOp || 'or' === $relOp) {
                $result .= strtoupper($relOp) . ' ';
            }
        }
	
		return $result;	
	}

	/**
	 * @param	string	$path
	 * @return	null
	 */
	protected function setTplPath($path)
	{
		if (! is_string($path) && ! empty($path)) {
			$err = "tpl path must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->tplPath = $path;
	}

    /**
     * Clear out any generated parts so this object can be reused.
     *
     * @return  SqlSelectBuilder
     */
    public function clear()
    {
        $this->data = array(
            'keywords'	=> array(),
            'columns'	=> array(),
            'from'		=> null,
            'joins'		=> array(),
            'where'		=> null,
            'group'		=> null,
            'having'	=> null,
            'order'		=> null,
            'limit'		=> null,
        );

        $this->values = array(
            'columns' => array(),
            'joins'   => array(),
            'where'   => array(),
            'group'   => array(),
            'having'  => array(),
            'order'   => array(),
            'limit'   => array(),
        );
    }
}
