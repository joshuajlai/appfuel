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
namespace Appfuel\Db\Schema;

use Appfuel\Framework\Exception,
	Appfuel\Framework\DataStructure\Dictionary,
	Appfuel\Framework\Db\Schema\ColumnParserInterface;

/**
 * Parser a string that represents a column into a dictionary
 */
class ColumnParser implements ColumnParserInterface
{
	/**
	 * Error text detailing what when wrong
	 * @var string
	 */
	protected $error = null;

	/**
	 * @return	bool
	 */
	public function isError()
	{
		return	! empty($this->error) && is_string($this->error);
	}

	/**
	 * @return	string
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @param	string	$str
	 * @return	Dictionary
	 */
	public function parseColumn($str)
	{
		$result = $this->extractColumnName($str);	
		if (false === $result) {
			return false;
		}

		$columnName = $result['column-name'];
		$result		= $this->extractDataType($result['input-string']);
		if (false === $result) {
			return false;
		}
		$dataType   = $result['data-type'];
		$modifier   = $result['modifier'];
		$str        = $result['input-string'];
		$isNullable = false;
		$found = stripos($str, 'not null');
		if (false !== $found) {
			$isNullable = true;
			$str = trim(str_ireplace('not null', '', $str));
		}
		$results = $this->extractDefault($str);
		echo "\n", print_r($results,1), "\n";exit;
		return new Dictionary();
	}

	/**
	 * Extract the column name from the string. With an identifier it will
	 * ignore spaces and grab including the identifier. With no identifier
	 * it grabs up to the first whitespace (tab or space but no newline)
	 *
	 * @param	string	$str
	 * @return	string
	 */
	public function extractColumnName($str)
	{
		$err = "parse error:";
		$str = trim($str);
		$identifiers = array("'", '"', "`");
		$first = $str{0};
		if (in_array($first, array("'", '"', '`'))) {
			$last = strpos($str, $first, 1);
			if (false === $last) {
				$this->setError("$err no end identifier for -($first)");
				return false;
			}
			$column = substr($str, 0, $last+1);
		}
		else {
			$column = strtok($str, " \t");
		}
		
		if (empty($column)) {
			$this->setError("$err column name can not be empty");
			return false;
		}

		return array(
			'column-name'  => $column,
			'input-string' => trim(substr($str, strlen($column)))
		);
	}

	/**
	 * @param	string
	 * @return	array
	 */
	protected function extractDataType($str)
	{
		$err = "parse error:";
		$str = trim($str);
		$start = strpos($str, '(');
			
		if (false !== $start) {
			$max = strlen($str);
			$end = null;
			for ($i=$start; $i < $max; $i++) {
				$prev = $str{$i - 1};
				$char = $str{$i};
				if (')' === $char && '\\' !== $prev) {
					$end = $i;
					break;
				}
			}
			if (null === $end) {
				$err .= "malformed parenthese pair starting at -($start)";
				$this->setError($err);
				return false;
			}
			$dataType = substr($str, 0, $start);
			$modifier = substr($str, $start+1, $end - $start-1);
			
			$end++;
		}
		else {
			$dataType = strtok($str, " \t");
			$modifier = null;
			$end = strlen($dataType) + 1;
		}

		$str = trim(substr($str, $end));
		return array(
			'data-type'		=> $dataType,
			'modifier'		=> $modifier,
			'input-string'	=> $str
		);
	}

	public function extractDefault($str)
	{
		$str   = trim($str);
		$found = stripos($str, 'default');
		if (false !== $found) {
			$end = strlen($str);
			$start = $found + 7;
			$valueStart = null;
			$valueEnd   = null;
			$beginChar  = null;
			for ($i=$start; $i < $end; $i++) {
				$prev = $str{$i-1};
				$char = $str{$i};
				$notEscaped = '\\' !== $prev;
				$isWhite    = ' ' === $char || '\t' === $char;
				$isSingle   = "'" === $char && $notEscaped;
				$isDouble   = '"' === $char && $notEscaped;
				$isOpen     = '(' === $char && $notEscaped;
				$isQuote    = $isSingle || $isDouble;

				echo "\n", print_r("char is : {$char}",1), "\n";
				if (null === $valueStart && $isWhite) {
					continue;
				}
				if (null === $valueStart && $isOpen) {
					$valueStart = $i;
					$beginChar  = ')';
					continue;
				}
				else if (null === $valueStart && $isQuote) {
					$valueStart = $i;
					$beginChar  = $char;
					continue;
				}
				else if (null === $valueStart && !$isQuote && !$isWhite &&
						 $beginChar !== '__NO_QUOTES__') {
					$valueStart = $i;
					$beginChar  = '__NO_QUOTES__';
					continue;
				}
				else if (null !== $valueStart && null === $valueEnd && 
						 $beginChar === $char && $notEscaped) {
					echo "\n", print_r('mark started quotes',1), "\n";
					$valueEnd = $i;
					break;
				}
				else if (null !== $valueStart && $beginChar === '__NO_QUOTES__'
						 && ! $isWhite) {
					$valueEnd = $i;
					continue;
				}
				else if (null !== $valueStart && $beginChar === '__NO_QUOTES__'
					&& $isWhite) {
				
					echo "\n", print_r("breaking ",1), "\n";
					$valueEnd = $i;
					break;
				}
					
			
			}
			echo "\n", print_r(array($str, $valueStart, $valueEnd),1), "\n";exit;
		}
		else {
			
		}
		
		echo "\n", print_r($token,1), "\n";exit;
	}

	/**
	 * @param	string	$str
	 * @return	null
	 */
	protected function setError($str)
	{
		$this->error = $str;
	}
}
