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
	Appfuel\View\FileViewTemplate,
	Appfuel\View\Html\HtmlPage,
	Appfuel\ClassLoader\StandardAutoLoader,
	Appfuel\ClassLoader\AutoLoaderInterface;

/**
 */
class ViewBuilder implements ViewBuilderInterface
{
	public function createViewData($format, array $params = null)
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
