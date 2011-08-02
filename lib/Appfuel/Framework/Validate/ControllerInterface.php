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
namespace Appfuel\Framework\Validate;

/**
 * The controller is the facade that exposes a uniform interface for handling 
 * filters getting errors and clean data.
 */
interface ControllerInterface
{
    /**
     * @return array
     */
	public function addFilter($field, $error, $filter, array $param = null);
	public function validate($rawData);
	public function isError();
	public function getErrors();
	public function getClean($field);
	public function getAllClean();
}
