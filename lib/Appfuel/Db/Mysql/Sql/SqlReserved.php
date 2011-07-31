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
		'precision', 'primary', 'procedure', 'purge',
		'read', 'reads', 'real', 'references', 'regexp', 'release', 'rename', 
		'repeat', 'replace', 'require', 'restrict', 'return', 'revoke',
		'right', 'rlike',
 
		'schema', 'schemas', 'second_microsecond', 'select', 'sensitive', 
		'separator', 'set', 'show', 'smallint', 'soname', 'spatial', 
		'specific', 'sql', 'sqlexception', 'sqlstate', 'sqlwarning',
		'sql_big_result', 'sql_calc_found_rows', 'sql_small_result', 'ssl',
		'starting', 'straight_join',
		'table', 'terminated', 'then', 'tinyblob', 'tinyint', 'tinytext', 'to',
		'trailing', 'trigger', 'true',
		'undo', 'union', 'unique', 'unlock', 'unsigned', 'update', 'usage', 
		'use', 'using', 'utc_date', 'utc_time', 'utc_timestamp',
		'values', 'varbinary', 'varchar', 'varcharacter', 'varying',
		'when', 'where', 'with', 'write',
		'xor',
		'year_month',
		'zerofile'
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
