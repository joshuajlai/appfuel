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
namespace Appfuel\Db\Mysql\Sql;


/**
 * Validates if a string is located inside the words list
 */
class SqlReserved
{
	static protected $words = array(
		'add', 'all', 'alter', 'analyze', 'and', 'as', 'asc',
		'before', 'between', 'bigint', 'binary', 'blob', 'both', 'by',
		'call', 'cascade', 'case', 'change', 'char', 'character',
		'check', 'collate', 'column','condition', 'constraint', 'continue', 
		'convert', 'create', 'cross', 'current','current_date', 'current_time',
		'current_timestamp', 'current_user', 'cursor', 
		'database', 'databases', 'day_hour', 'day_microsecond', 'day_minute',
		'day_second', 'dec', 'decimal', 'declare', 'default', 'delayed',
		'delete', 'desc', 'describe', 'deterministic', 'distinct', 
		'distinctrow', 'div', 'double', 'drop', 'dual',
		'each', 'else', 'elseif', 'enclosed', 'escape', 'exists', 'exit', 
		'explain',
		'false', 'fetch', 'float', 'float4', 'float8', 'for', 'force', 
		'foreign', 'from', 'fulltext',
		'grant','group',
		'having', 'high_priority', 'hour_microsecond', 'hour_minute',
		'hour_second',
		'if', 'ignore', 'in', 'index', 'infile', 'inner', 'inout', 
		'insensitive', 'insert', 'int', 'int1', 'int2', 'int3', 'int4', 'int8',
		'integer', 'interval', 'into', 'is', 'iterate',
		'join',
		'key','keys', 'kill', 
		'leading', 'leave', 'left', 'like', 'limit', 'lines', 'load',
		'localtime', 'localtimestamp', 'lock', 'long', 'longblob', 'longtext',
		'loop', 'low_priority',
		'match', 'mediumint', 'mediumtext', 'middleint', 'minute_microsecond',
		'minute_second', 'mod', 'modifies', 'natural', 'not', 
		'no_write_to_binlog', 'null', 'numeric', 
		'on', 'optimize', 'option', 'optionally', 'or', 'order', 'out', 
		'outer','outfile',


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
	static public function isReserved($word)
	{
		if (empty($word) || ! is_string($word)) {
			return false;
		}

		return in_array(strtolower($word), self::$words);
	}

	/**
	 * @return	array
	 */
	static public function getWords()
	{
		return self::$words;
	}
}
