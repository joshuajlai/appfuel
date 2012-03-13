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
namespace Appfuel\DataSource\Db\Mysql\Sql\Select;

use InvalidArgumentException;

/**
 */
class MysqlSelectKeywords implements MysqlSelectKeywordsInterface
{
	/**
	 * List of mysql keywords used in a select stmt
	 * @var array
	 */
	protected $words = array(
		'ALL'				=> false, 
		'DISTINCT'			=> false, 
		'DISTINCTROW'		=> false,
		'HIGH_PRIORITY'		=> false,
		'STRAIGHT_JOIN'		=> false,
		'SQL_SMALL_RESULT'	=> false, 
		'SQL_BIG_RESULT'	=> false, 
		'SQL_BUFFER_RESULT'	=> false, 
		'SQL_CACHE'			=> false, 
		'SQL_NO_CACHE'		=> false, 
		'SQL_CALC_FOUND_ROWS' => false
	);

	/**
	 * @var string
	 */
	protected $separator = ' ';

	/**
	 * @return	string
	 */
	public function getSeparator()
	{
		return $this->separator;
	}

	/**
	 * @param	string	$sep
	 * @return	null
	 */
	public function setSeparator($sep)
	{
		if (! is_string($sep) || empty($sep)) {
			$err = 'keyword separator must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->separator = $sep;
	}

	/**
	 * @return	array
	 */
	public function getAllKeywords()
	{
		return array_keys($this->words);
	}

	/**
	 * @return	array
	 */
	public function getEnabledKeywords()
	{
		$result = array();
		foreach ($this->words as $word => $status) {
			if (true === $status) {
				$result[] = $word;
			}
		}

		return $result;
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */
	public function enableKeywords(array $list)
	{
		foreach ($list as $word) {
			$this->enableKeyword($word);
		}
	}

	/**
	 * @param	string	$word
	 * @return	null
	 */
	public function enableKeyword($word)
	{
		if (! is_string($word)) {
			$err = 'keyword must be a non empty string';
			throw new InvalidArgumentException($err);
		}
		$word = strtoupper($word);

		switch ($word) {
			case 'ALL':
				$this->words['ALL']			= true;
				$this->words['DISTINCT']	= false;
				$this->words['DISTINCTROW'] = false;				
				break;
			case 'DISTINCT':
				$this->words['ALL']			= false;
				$this->words['DISTINCT']	= true;
				$this->words['DISTINCTROW'] = false;				
				break;
			case 'DISTINCTROW':
				$this->words['ALL']			= false;
				$this->words['DISTINCT']	= false;
				$this->words['DISTINCTROW'] = true;				
				break;
			case 'HIGH_PRIORITY':
				$this->words['HIGH_PRIORITY'] = true;
				break;
			case 'STRAIGHT_JOIN':
				$this->words['STRAIGHT_JOIN'] = true;
				break;
			case 'SQL_SMALL_RESULT':
				$this->words['SQL_SMALL_RESULT'] = true;
				$this->words['SQL_BIG_RESULT']   = false;
				break;
			case 'SQL_BIG_RESULT':
				$this->words['SQL_SMALL_RESULT'] = false;
				$this->words['SQL_BIG_RESULT']   = true;
				break;
			case 'SQL_BUFFER_RESULT':
				$this->words['SQL_BUFFER_RESULT'] = true;
				break;
			case 'SQL_CACHE':
				$this->words['SQL_CACHE']	 = true;
				$this->words['SQL_NO_CACHE'] = false;
				break;
			case 'SQL_NO_CACHE':
				$this->words['SQL_CACHE']	 = false;
				$this->words['SQL_NO_CACHE'] = true;
				break;
			case 'SQL_CALC_FOUND_ROWS':
				$this->words['SQL_CALC_FOUND_ROWS'] = true;
				break;
		}

	}

	/**
	 * @return	null
	 */
	public function reset()
	{
		foreach ($this->words as $key => $status) {
			$this->words[$key] = false;
		}

		$this->separator = ' ';
	}

	/**
	 * @param	string	$sep
	 * @return	string
	 */
	public function build()
	{
		$enabled = $this->getEnabledKeywords();
		return implode($this->getSeparator(), $renabled);
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->build();
	}
}
