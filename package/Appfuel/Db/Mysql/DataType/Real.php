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
class Real extends Float
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
		parent::__construct($attrs);
		$this->setSqlString('real');
		$this->setValidatorName('datatype-real');
	}
}
