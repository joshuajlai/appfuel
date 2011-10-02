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
namespace Appfuel\Db\Mysql\DataType;

use Appfuel\Framework\Exception;

/**
 * Defines the sql string and what validator is used to validate this type
 */
class Float extends AbstractNumber
{
	/**
	 * Fixed assignements include the sql string and the name of the 
	 * validator used for this datatype. Mysql implements a bit as
	 * tinyint(1) 
	 *
	 * @param	string	$attrs space delimited string of attributes
	 * @return	TinyInt
	 */
	public function __construct($attrs = null)
	{
		parent::__construct('float', 'datatype-float', $attrs);
	}

    /**
     * Override to handle alternate m,d syntax for floats that is not support
	 * for integers
	 * 
     * @param   string  $attrString
     * @return  AbstractNumber
     */
    public function loadAttributes($attrString)
    {  
        if (! is_string($attrString)) {
            throw new Exception("Invalid attribute string");
        }

        $attrs = explode(' ', strtolower($attrString));
        if (! $attrs) {
            return $this;
        }
        
		foreach ($attrs as $attr) {
            /* 
			 * float can specify display with in two ways
			 * m   - where m is the total digits to dislay. normal syntax
			 * m,d - where m is the total digits to display
			 *		       d is the digits after the decimal. mysql syntax
			 */
            if (is_numeric($attr) || false !== strpos($attr, ',', 1)) {
                $this->setDisplayWidth($attr);
                continue;
            }

            if ('unsigned' === $attr) {
                $this->enableUnsigned();
                continue;
            }

            if ('zerofill' === $attr) {
                $this->enableZeroFill();
                continue;
            }

            if ('auto_increment' === $attr) {
                $this->enableAutoIncrement();
                continue;
            }
        }

        return $this;
    }

    /**
	 * Override to allow for the m,d which can be a string or a digit
	 *
     * @param   int|string $width
     * @return  AbstractNumber
     */
    public function setDisplayWidth($width)
    {
		$err  = "display width can be expressed in two ways: -(m | m,d) ";
		$err .= "m and d must be integers ";

		if (is_string($width)) {
			$width = explode(',', $width);
			$max = count($width);
			if (1 === $max) {
				$width = $width[0];
				if (empty($width) || ! is_numeric($width)) {
					$err  = "single display width detected but is empty ";
					$err .= "or is not an integer";
					echo "\n", print_r('insert here',1), "\n";exit;
					throw new Exception($err);
				}
				$width =(int)$width;
			}
			else if (2 === $max) {
				$m = $width[0];
				$d = $width[1];
				if (empty($m) || !is_numeric($m) || empty($d) ||
					! is_numeric($d)) {
					$err  = "syntax m,d detected but either m or d or ";
					$err .= "both is empty or not an integer";
					throw new Exception($err);
				}
				$m =(int) $m;
				$d =(int) $d;
				$width = "$m,$d";
			}
			else {
				throw new Exception($err);
			}
		}
		else if (! is_int($width)) {
			throw new Exception($err);
		}

		return $this->addAttribute('display-width', $width);

    }
}
