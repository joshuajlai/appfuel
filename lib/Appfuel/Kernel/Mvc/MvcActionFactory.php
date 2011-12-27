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
	Appfuel\View\AjaxTemplate,
	Appfuel\View\ViewTemplate,
	Appfuel\View\Html\HtmlDocTemplate,
	Appfuel\View\Html\HtmlDocInterface,
	Appfuel\View\Html\HtmlViewInterface,
	Appfuel\View\AjaxInterface,
	Appfuel\View\ViewInterface,
	Appfuel\Console\ConsoleViewTemplate,
	Appfuel\ClassLoader\StandardAutoLoader,
	Appfuel\ClassLoader\AutoLoaderInterface;

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
	 * Namespace parser is used to turn class names into paths so we 
	 * can check if a file exists without using the autoloader.
	 * @var	AutoLoaderInterface
	 */
	protected $loader = null;

	/**
	 * @param	string	$controllerClass
	 * @return	ActionFactory
	 */
	public function __construct($actionClass = null,
								AutoLoaderInterface $loader = null)
	{
		if (null === $actionClass) {
			$actionClass = 'ActionController';
		}
		$this->setActionClass($actionClass);

		/*
		 * Note that we use the load class from the lib directory. This 
		 * constant is set during intialization. I will refactor next to a 
		 * a path finder. (on a deadline right now) --rsb
		 */
		if (null === $loader) {
			$loader = new StandardAutoLoader(AF_LIB_PATH);
		}
		$this->loader = $loader;
	}

	/**
	 * @return	string
	 */
	public function getActionClass()
	{
		return $this->actionClass;
	}

	/**
	 * @return	AutoLoaderInterface
	 */
	public function getLoader()
	{
		return $this->loader;
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
	public function createMvcAction($route,
									$namespace, 
									MvcActionDispatcherInterface $dispatcher)
	{
		if (! is_string($namespace)) {
			throw new InvalidArgumentException(
				"Controller namespace must be a string"
			);
		}
		$class = "$namespace\\{$this->getActionClass()}";
		return new $class($route, $dispatcher);
	}

    /**
	 * Creates a template view based on a view strategy. 
	 * app-htmlpage		ViewFileTemplates when the application loads the page
	 * app-ajax			JsonTemplate for ajax requests
	 * app-console		ConsoleViewtemplate for cli apps
	 *
     * @return  ViewTemplateInterface
     */
    public function createView($namespace, $type)
    {  
        if (! is_string($type)) {
            throw new InvalidArgumentException(
				"type paramter must be a string"
			);
        }

        switch($type) {
			case 'html-page': 
				$view = $this->createHtmlPage($namespace);
				break;
            case 'html':
                $view = $this->createHtmlView($namespace);
                break;
            case 'ajax':
                $view = $this->createAjaxView($namespace);
                break;
            case 'console':
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

        $class  = "$namespace\HtmlView";
		$loader = $this->getLoader();
		$isView = $loader->loadClass($class);
		$view = (true === $isView)? new $class() : new ViewTemplate();
        if (! $view instanceof ViewInterface) {
            throw new RunTimeException(
				"html view does not use correct interface"
			);
        }

        return $view;
    }

	/**
	 * @param	string	$namespace
	 * @return	HtmlPageInterface
	 */
	public function createHtmlPage($namespace)
	{
		if (empty($namespace) || ! is_string($namespace)) {
			$err = 'for html pages mvc actions must give their namespace';
			throw new InvalidArgumentException($err);
		}

		$class = "$namespace\HtmlView";
		$loader = $this->getLoader();
		$isView = $loader->loadClass($class);
		if (! $isView) {
			throw new RunTimeException("html view -($class) does not exist");
		}
	
		$view = new $class();
	    if (! $view instanceof HtmlViewInterface) {
			$err  = 'html page does not implement Appfuel\View\Html\HtmlPage';
			$err .= 'Interface';
            throw new RunTimeException($err);
        }

		/*
		 * By default the html doc is Appfuel\View\Html\HtmlDocTemplate,
		 * however, if a class is given then I will create that class and check
		 * it against appfuels html doc interface
		 */
		$htmlDocClass = $view->getHtmlDocClass();
		if (! empty($htmlDocClass) && is_string($htmlDocClass)) {
			$htmlDoc = new $htmlDocClass();
			if (! ($htmlDoc instanceof HtmlDocInterface)) {
				$err  = 'html doc does not implement Appfuel\View\Html\Html';
				$err .= 'DocTemplateInterface';
				throw new RunTimeException($err);
			}
		}
		else {
			$htmlDoc = $this->createHtmlDoc();
		}

		/*
		 * if this view belongs to an html layout then create the layout
		 * set the view in the layout and replace the view with the layout
		 */
		$layoutClass = $view->getLayoutClass();
		if (! empty($layoutClass) && is_string($layoutClass)) {
			$layout = new $layoutClass();
			if (! ($layout instanceof HtmlLayoutInterface)) {
				$err  = 'html layout does not implement Appfuel\View\Html\Html';
				$err .= 'LayoutInterface';
				throw new RunTimeException($err);
			}

			$layout->setView($view);
			$view = $layout;
		}

		$pageClass = $view->getHtmlPageClass();
		if (! empty($pageClass) && is_string($pageClass)) {
			$page = new $pageClass($view, $htmlDoc);
		}
		else {
			$page = new HtmlPage($view, $htmlDoc);
		}

		return $page;
	}

    /**
     * @return  ConsoleViewTemplate
     */
    public function createConsoleView($namespace = null)
    {
        if (null === $namespace) {
            return new ConsoleViewTemplate();
        }

		$loader = $this->getLoader();
        $class  = "$namespace\ConsoleView";
		$isView = $loader->loadClass($class);
		$view = (true === $isView)? new $class() : new ConsoleViewTemplate();

        if (! $view instanceof ViewInterface) {
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
			return new AjaxTemplate();
		}
			
		$loader = $this->getLoader();
		$class  = "$namespace\AjaxView";
		$isView = $loader->loadClass($class);
		$view = (true === $isView)? new $class() : new AjaxTemplate();
        if (! $view instanceof AjaxInterface) {
            throw new RunTimeException(
				"json view does not use correct interface"
			);
        }

        return $view;
    }

	public function createHtmlDoc()
	{
		return new HtmlDocTemplate();
	}
}
