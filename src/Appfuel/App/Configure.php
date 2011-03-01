<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *  
 * @category    Appfuel
 * @package     App
 * @author      Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace Appfuel\App;

use Appfuel\StdLib\PhpEnv\PhpErrorInterface as ErrorInterface;

/**
 */
class Configure
{
	
	/**
	 * @return	ErrorInterface	
	 */	
	public function getError()
	{
		return $this->phpError;
	}

	/**
	 * @return	FALSE | old include path
	 */
	public function	setIncludePaths(array $paths)
	{
        if (empty($paths)) {
            throw new Exception(
                "Can not set include paths without a path"
            );
        }

        return set_include_path(implode(PATH_SEPARATOR, $paths));
	}

	/**
	 * @return	int
	 */ 
	public function errorLevel($code = NULL)
	{
		return error_reporting($code);
	}

	/**
	 * @return	NULL
	 */
	public function	displayErrors($flag)
	{
        if (is_string($flag)) {
            $flag = strtolower($flag);
        }

        $result = NULL;
        switch($flag) {
            case 'on':
            case TRUE:
            case 1:
            case 'yes':
                $result = $this->setDirective('display_errors', 1);
                break;
            case 'off':
            case FALSE:
            case 0:
            case 'no':
                $result = $this->setDirective('display_errors', 0);
                break;
        }

        return $result;
	}

	
	/**
	 * @return	scalar
	 */	
	public function setDirective($name, $value)
	{
		if (! is_string($name)) {
			throw new Exception(
				"Invalid directive name given as ($name) must be a string"
			);
		}

		if (! is_scalar($value)) {
			throw new Exception(
				"Invalid directive value. value must be a scalar value"
			);
		}

		return set_ini($name, $value);	
	}

	/**
	 * @return scalar
	 */
	public function getDirective($name)
	{
		if (! is_string($name)) {
			throw new Exception(
				"Invalid directive name given as ($name) must be a string"
			);
		}

		return ini_get($name);	
	}
}
