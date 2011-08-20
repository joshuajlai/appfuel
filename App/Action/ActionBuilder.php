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
namespace Appfuel\App\Action;


use Appfuel\Framework\Exception,
	Appfuel\Framework\App\Action\ActionBuilderInterface,
	Appfuel\Framework\App\Route\RouteInterface,
    Appfuel\Framework\App\ContextInterface,
    Appfuel\Framework\View\DocumentInterface,
	Appfuel\Framework\View\ViewManagerInterface,
	Appfuel\App\View\ViewManager;

/**
 *
 */
class ActionBuilder implements ActionBuilderInterface
{
	/**
	 * Used to determine the namespaces for objects required to build the
	 * controller
	 * @var RouteInterface
	 */
	protected $route = null;

	/**
	 * Text to decribe the error that has just occured
	 * @var string
	 */
	protected $error = null;

	/**
	 * flag used to indicate the build has an error
	 * @var bool
	 */
	protected $isError = false;

	/**
	 * @var bool
	 */
	protected $isInputValidation = true;

    /**
     * Whitelist of valid response types
     * @var array
     */
    protected $validResponseTypes = array(
        'Html',
        'Json',
        'Cli',
        'Csv'
    );

	/**
	 * @param	RouteInterface	$route
	 * @return	ActionBuilder
	 */
	public function __construct(RouteInterface $route)
	{
		$this->route = $route;
	}

	/**
	 * @return	RouteInterface
	 */
	public function getRoute()
	{
		return $this->route;
	}

	/**
	 * @return	bool
	 */
	public function isError()
	{
		return $this->isError;
	}

	/**
	 * @param	string	$text
	 * @return	ActionBuilder
	 */
	public function setError($text)
	{
		$this->error = $text;
		$this->isError = true;
		return $this;
	}

	/**
	 * @return	string
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * @return ActionBuilder
	 */
	public function clearError()
	{
		$this->error   = null;
		$this->isError = false;
		return $this;
	}

    /**
     * @param   string  $responseType
     * @return  bool
     */
    public function isValidResponseType($type)
    {
        return in_array($type, $this->validResponseTypes);
    }

    /**
     * @param   string  $responseType
     * @return  ActionBuilder
     */
    public function addValidResponseType($type)
    {
        if (! is_string($type) || empty($type)) {
            throw new Exception("Response type must be a non empty string");
        }

        $this->validResponseTypes[] = $type;
        return $this;
    }

    /**
     * @param   string  $responseType
     * @return  ViewManager
     */
    public function removeValidResponseType($type)
    {
        if (! is_string($type) || empty($type)) {
            throw new Exception("Response type must be a non empty string");
        }

        $isStrict = true;
        $key = array_search($type, $this->validResponseTypes, $isStrict);
        if ($key !== false) {
            /* remove item and reorder index */
            unset($this->validResponseTypes[$key]);
            $this->validResponseTypes = array_values(
                $this->validResponseTypes
            );
        }

        return $this;
    }

    /**
     * @return  ViewManager
     */
    public function clearValidResponseTypes()
    {
        $this->validResponseTypes = array();
        return $this;
    }

    /**
     * @return  ViewManager
     */
    public function setValidResponseTypes(array $types)
    {
        foreach ($types as $type) {
            $this->addValidResponseType($type);
        }
        return $this;
    }

    /**
     * @return  array
     */
    public function getValidResponseTypes()
    {
        return $this->validResponseTypes;
    }

	/**
	 * @return	ControllerInterface
	 */
	public function createController()
	{
		$route = $this->getRoute();
		$ns    = $route->getActionNamespace();
		$class = "$ns\\Controller";
    
		try {
			$controller = new $class();
		} catch (Exception	$e) {
			$this->setError("Controller for ($class) could not be found");
			return false;
		}

		return $controller;
	}

	/**
	 * @return bool
	 */
	public function isInputValidation()
	{
		return $this->isInputValidation;
	}

	/**
	 * @return	ActionBuilder
	 */
	public function enableInputValidation()
	{
		$this->isInputValidation = true;
		return $this;
	}

	/**
	 * @return	ActionBuilder
	 */
	public function disableInputValidation()
	{
		$this->isInputValidation = false;
		return $this;
	}

	/**
	 * @param	string	$type 
	 * @return	ViewInterface
	 */
	public function createViewResponse($type)
	{
        $type  = ucfirst($type);
        if (! $this->isValidResponseType($type)) {
			$this->setError("Invalid response type for $type");
            return false;
        }

        $class = "\\Appfuel\\App\\View\\{$type}\\Response";

		try {
			$view = new $class();
		} catch (Exception $e) {
			$this->setError("Could not instantiate class $class");
			return false;
		}

		return $view;
	}

	/**
	 * @return ViewManager
	 */
	public function createViewManager()
	{
		return new ViewManager();
	}

	/**	
	 * @param	string	$responseType
	 * @return	ControllerInterface
	 */
	public function buildController($responseType)
	{
		$controller = $this->createController();
		if (! $controller) {
			return false;
		}

		$view = $this->createViewResponse($responseType);
		if (! $view) {
			return false;
		}

		$manager = $this->createViewManager();
		$manager->setView($view);
		
		$controller->setViewManager($manager);

		return $controller;
	}
}
