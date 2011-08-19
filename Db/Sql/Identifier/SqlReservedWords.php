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
namespace Appfuel\Db\Sql\Identifier;

use Appfuel\Framework\Db\Sql\SqlReservedWordsInterface;

/**
 * Validates if a string is located inside the words list
 */
class SqlReservedWords implements SqlReservedWordsInterface
{
	/**
	 * @var array
	 */
	protected $words = array(
		'add', 'all', 'allocate', 'alter', 'and', 'any', 'are', 'as', 'asc',
		'assertion', 'at', 'authorization', 'avg', 
		'begin', 'between', 'bigint', 'bit', 'boolean', 'both', 'by',
		'call', 'cascade', 'cascaded', 'case', 'cast', 'char', 'character',
		'check', 'close', 'coalesce', 'collate', 'collation', 'column',
		'commit', 'connect', 'connection', 'constraint', 'constraints',
		'continue', 'convert', 'corresponding', 'create', 'cross', 'current',
		'current_date', 'current_role', 'current_time', 'current_timestamp',
		'current_user', 'cursor', 
		'deallocate', 'dec', 'decimal', 'declare', 'default', 'deferrable',
		'deferred', 'delete', 'desc', 'describe', 'diagnostics', 'disconnect',
		'distinct', 'double', 'drop',
		'else', 'end', 'end-exec', 'escape', 'except', 'exception', 'exec',
		'execute', 'exists', 'explain', 'external',
		'false', 'fetch', 'first', 'float', 'for', 'foreign', 'found', 'from',
		'full', 'function',
		'get', 'getcurrentconnection', 'global', 'go', 'goto', 'grant','group',
		'having', 'hour',
		'identity', 'immediate', 'in', 'indicator', 'initially', 'inner',
		'inout', 'input', 'insensitive', 'insert', 'int', 'integer', 
		'intersect', 'into', 'is', 'isolation',
		'join',
		'key',
		'last', 'left', 'like', 'lower', 'ltrim',
		'match', 'max', 'min', 'minute',
		'national', 'natural', 'nchar', 'nvarchar', 'next', 'no', 'none', 
		'not', 'null', 'nullif', 'numeric',
		'of', 'on', 'only', 'open', 'option', 'or', 'order', 'outer', 'output',
		'over', 'overlaps',
		'pad', 'partial', 'prepare', 'preserve', 'primary', 'prior', 
		'privileges', 'procedure', 'public',
		'read', 'real', 'references', 'relative', 'restrict', 'revoke', 
		'right', 'rollback', 'rows', 'row_number', 'rtrim',
		'schema', 'scroll', 'second', 'select', 'session_user', 'set', 
		'smallint', 'some', 'space', 'sql', 'sqlcode', 'sqlerror', 'sqlstate',
		'substr', 'substring', 'sum', 'system_user',
		'table', 'temporary', 'timezone_hour', 'timezone_minute', 'to',
		'transaction', 'translate', 'translation', 'trim', 'true',
		'union', 'unique', 'unknown', 'update', 'upper', 'user', 'using',
		'values', 'varchar', 'varying', 'view',
		'whenever', 'where', 'with', 'work', 'write',
		'xml', 'xmlexists', 'xmlparse', 'xmlquery', 'xmlserialize',
		'year',
	);

	/**
	 * @param	string	$word
	 * return	bool
	 */
	public function isReserved($word)
	{
		if (empty($word) || ! is_string($word)) {
			return false;
		}

		return in_array(strtolower($word), $this->words);
	}

	/**
	 * @return	array
	 */
	public function getWords()
	{
		return $this->words;
	}
}
