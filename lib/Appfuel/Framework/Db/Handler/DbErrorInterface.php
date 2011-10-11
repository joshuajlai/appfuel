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
namespace Appfuel\Framework\Db\Handler;

/**
 * Represent a generic database error that has no working knowledge of vendor
 * specific database adapters
 */
interface DbErrorInterface
{
	/**
	 * Holds the code of the error that has occured. Gerenally vendor specific 
	 * codes are set here
	 * 
	 * @return	scalar
	 */
	public function getCode();

	/**
	 * Text specific to the error 
	 *
	 * @return	string
	 */
	public function getMessage();

	/**
     * A 5 character string specified by the ANSI SQL and ODBC this is a
     * a more standardized error code
     * 
	 * @return	string
	 */
	public function getSqlState();

	/**
	 * Allow the error to be used in the context of a string
	 * 
	 * @return	string
	 */
	public function __toString();

}
