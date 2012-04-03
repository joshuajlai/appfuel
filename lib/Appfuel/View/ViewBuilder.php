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
namespace Appfuel\View;

use Exception,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\Console\ConsoleTemplate,
	Appfuel\View\CsvTemplate,
    Appfuel\View\AjaxTemplate,
	Appfuel\View\ViewTemplate,
	Appfuel\View\ViewInterface,
	Appfuel\View\ViewCompositor,
	Appfuel\View\FileViewTemplate,
	Appfuel\Html\HtmlPage,
	Appfuel\Html\HtmlPageConfiguration,
	Appfuel\Html\Tag\HtmlTagFactory,
	Appfuel\Html\Tag\HtmlTagFactoryInterface,
	Appfuel\Kernel\Mvc\MvcContextInterface,
	Appfuel\Kernel\Mvc\MvcRouteDetailInterface;

/**
 */
class ViewBuilder implements ViewBuilderInterface
{
	/**
	 * List of supported formats that can be composed into a view
	 * @var array
	 */
	protected $validFormats = array('json', 'html', 'text', 'csv');

	/**
	 * @param	MvcContextInterface		$context
	 * @param	MvcRouteDetailInterface $detail
	 * @return	null
	 */
	public function setupView(MvcContextInterface $context,
							  MvcRouteDetailInterface $route,
							 $format)
	{
		if ($route->isView() && ! $route->isManualView()) {
			$context->setViewFormat($format);
			if ('html' === $format) {
				$htmlPage = $this->createHtmlPage();
				if ($route->isViewPackage()) {
					$config = $this->createHtmlPageConfiguration();
					$config->applyView($route->getViewPackage(), $htmlPage);
				}

				$context->setView($htmlPage);
			}
			else {
				$context->setView(new ViewTemplate());
			}
		}
	}

	/**
	 * @param	MvcContextInterface $context
	 * @param	MvcRouteDetailInterface $route
	 * @return	string
	 */
	public function composeView(MvcContextInterface $context,
								MvcRouteDetailInterface $route)
	{
		if (! $route->isView()) {
			return '';
		}

		if ($route->isManualView()) {
			return $context->getView();
		}
		
		$format = $context->getViewFormat();
		if (! $this->isFormatSupported($format)) {
			$err = 'could not compose view: format not found';
			throw new DomainException($err, 404);
		}

		$data = $context->getView();
		$compositor = $this->createViewCompositor('file');
	}

	/**
	 * @param	ViewDataInterface $data
	 * @return	string
	 */
	public function createViewCompositor($format)
	{
		if (! is_string($format) || empty($format)) {
			$err = "compositor strategy must be a non empty string";
			throw new InvalidArgumentException($err);
		}
		$class = 'Appfuel\View\Compositor\\' . ucfirst($format) . 'Compositor';
		//$obj = new $class();
		$file = 'appfuel/web/app-view/test/mytest.phtml';
		$data = array('foo' => 'bar');
		echo "<pre>", print_r(ViewCompositor::composeFile($file, $data), 1), "</pre>";exit;
	}

	/**
	 * @param	string	$format
	 * @return	bool
	 */
	public function isFormatSupported($format)
	{
		if (is_string($format) && in_array($format,$this->validFormats,true)) {
			return true;
		}

		return false;
	}

	/**
	 * @param	HtmlTagFactoryInterface $factory
	 * @return	HtmlPage
	 */
	public function createHtmlPage(HtmlTagFactoryInterface $factory = null)
	{
		if (null === $factory) {
			$factory = $this->createHtmlTagFactory();
		}
		return new HtmlPage();
	}

	/**
	 * @return	HtmlTagFactory
	 */
	public function createHtmlTagFactory()
	{
		return new HtmlTagFactory();
	}

	/**
	 * @return	HtmlPageConfiguration
	 */
	public function createHtmlPageConfiguration()
	{
		return new HtmlPageConfiguration();
	}

	/**
	 * @param	string	$format
	 * @param	array	$params
	 * @return	ViewData
	 */
	public function createViewData($format = null, array $params = null)
	{
		if ('html' === strtolower($format)) {
			$data = new HtmlPage();
		}
		else {
			$data = new ViewData();
		}

		return $data;
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


		/*
		 * override the view by just instantiating the view class given
		 */
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
		
		/*
		 * Do not need a template, just use this raw string
		 */
		if ($viewDetail->isRawView()) {
			return $viewDetail->getRawView();
		}

		/*
		 * no overrides use strategy given to build default view
		 */
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

            case 'csv':
                $view = $this->createDefaultCsvTemplate();
                break; 

			default:
				$err = "strategy -($strategy) not mapped";
				throw new RunTimeException($err);
		}

		return $view;
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

	/**
     * Create a standard view template with a csv compositor instead of 
     * a file compositor.
	 * @return	CsvTemplate
	 */
	public function createDefaultCsvTemplate()
	{
		return new CsvTemplate();
	}
}
