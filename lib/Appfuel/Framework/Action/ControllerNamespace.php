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
namespace Appfuel\Framework\Action;


use Appfuel\Framework\Exception;

/**
 * Parse out the individual namespaces of the action controller so that the 
 * front controller has a list of all the parent namespaces of the this
 * action controller. Also holds the full classname of the action controller
 */
class ControllerNamespace implements ControllerNamespaceInterface
{
	/**
	 * The root level namespace is the top level namespace that holds 
	 * all modules, sub modules and actions
	 * @var string
	 */
	protected $rootNs = null;
	
	/**
	 * Namespace of the module that contains all the sub modules
	 * @var string
	 */
	protected $moduleNs = null;
	
	/**
	 * Namespace of the sub module that holds all the actions
	 * @vat string
	 */
	protected $subModuleNs = null;
	
	/**
	 * Namespace of the action that holds the controller and other supporting
	 * classes
	 * @var string
	 */
	protected $actionNs = null;

	/**
	 * Fully qualifed namespace for the action controller
	 * @var string
	 */
	protected $controllerClass = null;

	/**
	 * @param	string	$actionNs	full namespace of the action controller
	 * @return	ActionControllerDetail
	 */
	public function __construct($actionNs)
	{
	    $errText = "Invalid namespace give :";
        if (! $this->isValidString($actionNs)) {
            throw new Exception("$errText must be a non empty string");
        }
        $this->actionNs = $actionNs;
		$this->controllerClass = "$actionNs\Controller";

        $pos       = strrpos($actionNs, '\\');
        $subModule = substr($actionNs, 0, $pos);
        if (! $this->isValidString($subModule)) {
            throw new Exception(
                "$errText sub module namespace must be a non empty string");
        }
        $this->subModuleNs = $subModule;

        $pos    = strrpos($subModule, '\\');
        $module = substr($subModule, 0, $pos);
        if (! $this->isValidString($module)) {
            throw new Exception(
                "$errText module namespace must be a non empty string");
        }
        $this->moduleNs = $module;

        $pos  = strrpos($module, '\\');
        $root = substr($module, 0, $pos);
        if (! $this->isValidString($root)) {
            throw new Exception(
                "$errText root action namespace must be a non empty string");
        }
        $this->rootNs = $root;
	}

	/**
	 * @return	string	
	 */
	public function getControllerClass()
	{
		return $this->controllerClass;
	}

    /**
     * @return string
     */
    public function getActionNamespace()
    {
        return $this->actionNs;
    }

    /**
     * @return string
     */
    public function getSubModuleNamespace()
    {
        return $this->subModuleNs;
    }

    /**
     * @return string
     */
    public function getModuleNamespace()
    {
        return $this->moduleNs;
    }

    /**
     * @return string
     */
    public function getRootNamespace()
    {
        return $this->rootNs;
    }

    /**
     * @param   string $str
     * @return  bool
     */
    protected function isValidString($str)
    {
        if (is_string($str) && ! empty($str)) {
            return true;
        }

        return false;
    }
}
