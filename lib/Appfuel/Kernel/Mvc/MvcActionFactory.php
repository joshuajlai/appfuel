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
namespace Appfuel\Kernel\Mvc;

use Exception,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\View\JsonTemplate,
	Appfuel\Console\ConsoleViewTemplate,
	Appfuel\Framework\View\JsonTemplateInterface,
	Appfuel\Framework\Console\ConsoleViewTemplateInterface;

/**
 * Used to build action controllers
 */
class MvcActionFactory implements MvcActionFactoryInterface
{
	/**
	 * The php class name of an action controller
	 * @var string
	 */
	protected $actionClass = null;

	/**
	 * @param	string	$controllerClass
	 * @return	ActionFactory
	 */
	public function __construct($actionClass = null)
	{
		if (null === $actionClass) {
			$actionClass = 'ActionController';
		}
		$this->setActionClass($actionClass);
	}

	/**
	 * @return	string
	 */
	public function getActionClass()
	{
		return $this->actionClass;
	}

	/**
	 * @param	string	$className
	 * @return	ActionFactory
	 */
	public function setActionClass($className)
	{
		if (empty($className) || ! is_string($className)) {
			throw new InvalidArgumentException(
				"Controller class name  must be a non empty string"
			);
		}

		$this->actionClass = $className;
		return $this;
	}

	/**
	 * @param	string	$namespace
	 * @return	ActionControllerInterface
	 */
	public function createMvcAction($namespace)
	{
		if (! is_string($namespace)) {
			throw new InvalidArgumentException(
				"Controller namespace must be a string"
			);
		}
		$class = "$namespace\\{$this->getActionClass()}";
		return new $class();
	}

    /**
     * @return  ViewTemplateInterface
     */
    public function createView($namespace, $type = null)
    {  
        if (null === $type) {
            if (! defined('AF_APP_TYPE')) {
                throw new RunTimeException("constant AF_APP_TYPE not declared");
            }
            $type = AF_APP_TYPE;
        }

        if (! is_string($type)) {
            throw new Exception("type paramter must be a string");
        }

        /*
         */
        switch($type) {
            case 'app-htmlpage':
                $view = $this->createHtmlView($namespace);
                break;
            case 'app-ajax':
                $view = $this->createJsonView($namespace);
                break;
            case 'app-console':
                $view = $this->createConsoleView($namespace);
                break;
            default:
                $view = new ViewTemplate();
        }

        return $view;
    }

    /**
     * @return  HtmlViewTemplate
     */
    public function createHtmlView($namespace = null)
    {
        if (null === $namespace) {
            return new ViewTemplate();
        }

        $class = "$namespace\HtmlView";
        try {
            $view = new $class();
        } catch (Exception $e) {
            $view = new ViewTemplate();
        }

        if (! $view instanceof ViewTemplateInterface) {
            throw new Exception("console view does not use correct interface");
        }

        return $view;
    }

    /**
     * @return  ConsoleViewTemplate
     */
    public function createConsoleView($namespace = null)
    {
        if (null === $namespace) {
            return new ConsoleViewTemplate();
        }

        $class = "$namespace\ConsoleView";
        try {
            $view = new $class();
        } catch (Exception $e) {
            $view = new ConsoleViewTemplate();
        }

        if (! $view instanceof ConsoleViewTemplateInterface) {
            throw new RunTimeException(
				"console view does not use correct interface"
			);
        }

        return $view;
    }

    /**
     * @return  JsonTemplate
     */
    public function createAjaxView($namespace = null)
    {
		if (null === $namespace) {
			return new JsonTemplate();
		}
			
		$class = "$namespace\AjaxView";
		try {
            $view = new $class();
        } catch (Exception $e) {
            $view = new JsonTemplate();
        }

        if (! $view instanceof JsonTemplateInterface) {
            throw new RunTimeException(
				"json view does not use correct interface"
			);
        }

        return $view;
    }
}
