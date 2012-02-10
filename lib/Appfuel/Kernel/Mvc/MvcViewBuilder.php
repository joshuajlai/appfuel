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
	Appfuel\Console\ConsoleTemplate,
	Appfuel\View\AjaxTemplate,
	Appfuel\View\ViewTemplate,
	Appfuel\View\ViewInterface,
	Appfuel\View\FileViewTemplate,
	Appfuel\View\Html\HtmlTemplate,
    Appfuel\View\Html\HtmlPageBuilder,
    Appfuel\View\Html\HtmlPageBuilderInterface,
	Appfuel\ClassLoader\StandardAutoLoader,
	Appfuel\ClassLoader\AutoLoaderInterface;

/**
 */
class MvcViewBuilder implements MvcViewBuilderInterface
{
    /**
     * The autoloader is used to determine if the class exists. 
     * @var AutoLoaderInterface
     */
    protected $loader = null;

    /**
     * Used to build and configure html pages
     *
     * @var HtmlPageBuilderInterface
     */
    protected $pageBuilder = null;

    /**
     * @param   string  $controllerClass
     * @return  MvcActionBuilder
     */
    public function __construct(AutoLoaderInterface $loader = null,
                                HtmlPageBuilderInterface $builder = null)
    {
        if (null === $loader) {
            $loader = new StandardAutoLoader(AF_LIB_PATH);
        }
        $this->setClassLoader($loader);

        if (null === $builder) {
            $builder = new HtmlPageBuilder();
        }
        $this->setHtmlPageBuilder($builder);
    }


    /**
     * @return  AutoLoaderInterface
     */
    public function getClassLoader()
    {
        return $this->loader;
    }

    /**
     * @param   AutoLoaderInterface $loader
     * @return  MvcActionBuilder
     */
    public function setClassLoader(AutoLoaderInterface $loader)
    {
        $this->loader = $loader;
        return $this;
    }

    /**
     * @return  HtmlPageBuilderInterface
     */
    public function getHtmlPageBuilder()
    {
        return $this->pageBuilder;
    }

    /**
     * @param   HtmlPageBuilderInterface
     * @return  MvcContextBuilder
     */
    public function setHtmlPageBuilder(HtmlPageBuilderInterface $builder)
    {
        $this->pageBuilder = $builder;
        return $this;
    }

	/**
	 * @param	MvcViewDetailInterface	$detail
	 * @return	ViewInterface | empty string
	 */
	public function buildView(MvcRouteDetailInterface $detail)
	{
		$viewDetail = $detail->getViewDetail();
		if (! $viewDetail->isView()) {
			return '';
		}

		$namespace  = $detail->getNamespace();
		$strategy = $viewDetail->getStrategy();
		$params   = $viewDetail->getParams();
		$method   = $viewDetail->getMethod();
		$class    = $viewDetail->getViewClass();

		if ($viewDetail->isViewClass()) {
			return new $class();
		}

		
		if (is_string($method) && is_callable(array($this, $method))) {
			return $this->$method($strategy, $params);
		}

		if (is_callable($method)) {
			return call_user_func($method, $strategy, $params);
		}

		if (null !== $method) {
			$name = 'unkown';
			if (is_string($method)) {
				$name = $method;
			}
			else if (is_array($method) && isset($method[1])) {
				$name = $method[1];
			}
			$err = "view build failed: method not found -($name)";
			throw new RunTimeException($err);
		}
		
		$view = $this->buildAppView($strategy, $params);
		return $view;
	}

	/**
	 * @param	string	$strategy
	 * @param	array	$params
	 * @return	ViewInterface
	 */
	public function buildAppView($strategy, array $params)
	{
		switch($strategy) {
			case 'html-page':
				$view = $this->buildHtmlPage($params);
				break;

			case 'html': 
				$view = $this->createDefaultHtmlTemplate();
				break;

			case 'ajax':
				$view = $this->createDefaultAjaxTemplate();
				break;
	
			case 'console':
				$view = $this->createDefaultConsoleTemplate();
				break;

			case 'general':
				$view = $this->createViewTemplate();
				break;

			default:
				$err = "strategy -($strategy) not mapped";
				throw new RunTimeException($err);
		}

		return $view;
	}

	/**
	 * @param	array	$data
	 * @return	mixed
	 */
	public function buildCustomView($strategy, array $data)
	{
		if (! isset($data['custom-class'])) {
			$err = 'custom key -(custom-class) not found';
			throw new InvalidArgumentException($err);
		}
		
		$class = $data['custom-class'];
		if (! is_string($class) || empty($class)) {
			$err = 'view class was given but was not a string or empty';
			throw new InvalidArgumentException($err);
		}
        
		if (! $this->isExtendedView($class)) {
			$err = "view class -($class) can not be found";
			throw new RunTimeException($err);
		}

		return new $class();
	}

	/**
	 * @param	string	$class
	 * @return	bool
	 */
	public function isExtendedView($class)
	{
		if (! is_string($class) || empty($class)) {
			return false;
		}

		return $this->getClassLoader()
					->loadClass($class);
	}

	/**
	 * @return	ConsoleTemplate
	 */
	public function createDefaultConsoleTemplate()
	{
		return new ConsoleTemplate();
	}

	/**
	 * @return	AjaxTemplate
	 */
	public function createDefaultAjaxTemplate()
	{
		return new AjaxTemplate();
	}

	/**
	 * @return	HtmlTemplate
	 */
	public function createDefaultHtmlTemplate()
	{
		return new HtmlTemplate();
	}

	/**
	 * @return	ViewTemplate
	 */
	public function createViewTemplate()
	{
		return new ViewTemplate();
	}
}
