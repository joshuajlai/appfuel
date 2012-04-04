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

use DomainException,
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
			$view = $this->createTemplate($format);
			$context->setView($view);
			if ('html' === $format) {
				if ($route->isViewPackage()) {
					$config = $this->createHtmlPageConfiguration(AF_BASE_URL);
					$config->applyView($route->getViewPackage(), $view);
				}
			}
		}
	}

	public function createTemplate($format)
	{
		switch ($format) {
			case 'html': $template = $this->createHtmlPage();	  break;
			case 'csv' : $template = $this->createCsvTemplate();  break;
			case 'json': $template = $this->createAjaxTemplate(); break;
			case 'text': $template = $this->createTextTemplate(); break;
			default: 
				$err = "could not find template for format -($format)";
				throw new DomainException($err, 404);
		}

		return $template;
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
	public function createHtmlPageConfiguration($url)
	{
		return new HtmlPageConfiguration($url);
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
	 * @return	AjaxTemplate
	 */
	public function createAjaxTemplate()
	{
		return new AjaxTemplate();
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
	public function createCsvTemplate()
	{
		return new CsvTemplate();
	}
}
