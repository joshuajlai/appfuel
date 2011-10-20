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
	 * @return	ColumnParser
	 */
	public function clearError()
	{
		$this->error = null;
		return $this;
	}

	/**
	 * Extract the column name from the string. With an identifier it will
	 * ignore spaces and grab including the identifier. With no identifier
	 * it grabs up to the first whitespace (tab or space but no newline). 
	 * Returns false and error and an array with the following structure:
	 * array (
	 *	'column-name'  => <string-value>,
	 *  'input-string' => <string-value>
	 * )
	 *
	 * @param	string	$str
	 * @return	array | false on failure
	 */
	public function extractColumnName($str)
	{
		$err = "parse error:";
		$str = $this->filterInputString($str);
		if (false === $str) {
			return false;
		}

		$first = $str{0};
		if (in_array($first, array("'", '"', '`'))) {
			$last = strpos($str, $first, 1);
			if (false === $last) {
				$this->setError("$err no end identifier for -($first)");
				return false;
			}
			$column = substr($str, 0, $last+1);
			if (strlen($column) <= 2) {
				$this->setError("$err column name can not be empty");
				return false;
			}
		}
		else {
			$column = strtok($str, " \t");
		}
	
		return array(
			'column-name'  => $column,
			'input-string' => trim(substr($str, strlen($column)))
		);
	}

	/**
	 * This assumes the column name has already been extracted, making the
	 * the datatype the first paramater. This method does not attempt to pull 
	 * all the data type attributes out of the string because those can be 
	 * largely vendor specific. Instead it extracts the data type name 
	 * what I call a type modifier. This expects the column name has
	 * been extracted so the datatype is the next token. Type modifier is
	 * any thing in parentheses and can be interpreted by vendor specific code
	 * sql validator and generators. 
	 *
	 * @param	string
	 * @return	array
	 */
	public function extractDataType($str)
	{
		$err = "parse error:";
		$str = $this->filterInputString($str);
		if (false === $str) {
			return false;
		}
	
		/* 
		 * Both start and end of the parenthese are found and they do 
		 * not belong to the parentheses of the default keyword
		 */
		$start = strpos($str, '(');
		$end   = strpos($str, ')');
		$default = stripos($str, 'default');
		if ((false !== $start && false !== $end) &&
			(false === $default || ($start < $default && $end < $default))) {
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
		/* either no parenthese exist at all or they belong to the default
		 * keyword
		 */
		else if ((false === $start && false === $end) || 
				 (false !== $default && 
				  ($start > $default && $end > $default))) {
			$dataType = strtok($str, " \t");
			$modifier = null;
			$end = strlen($dataType) + 1;
		}
		else {
			$err .= " malformed parenthese pair start detected at -($start) ";
			$err .= "close detected at -($end)";
			$this->setError($err);
			return false;
		}

		return array(
			'data-type'		=> trim($dataType),
			'modifier'		=> $modifier,
			'input-string'	=> trim(substr($str, $end))
		);
	}
	
	/**
	 * @param	string
	 * @return	string	| false on error
	 */
	protected function filterInputString($str)
	{
		$err = "parse error:";
		if (empty($str) || ! is_string($str)) {
			$this->setError("$err input must be a non empty string");
			return false;
		}
	
		/* whitespaces are considered empty */
		$str = trim($str);
		if (empty($str)) {
			$this->setError("$err input string can not be all whitespaces");
			return false;
		}
		return $str;
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
