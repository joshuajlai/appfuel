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
					$config = $this->createHtmlPageConfiguration(
                        AF_RESOURCE_URL, AF_IS_RESOURCE_BUILD
                    );
					$config->applyView($route->getViewPackage(), $view);
				}
			}
		}
	}

	/**
	 * Uses the route detail to detemine if the view is disabled, also
	 * checks the data type of the view. Eventhough, ViewInterface implements
	 * __toString, it has the possibility of throwing exceptions which in
	 * __toString leaves a nasty little error that says simply, you can
	 * not throw exceptions in __toString. To avoid this we run build instead
	 *
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

		$view = $context->getView();
		if (is_string($view)) {
			$result = $view;
		}
		else if ($view instanceof ViewInterface) {
			$result = $view->build();
		}
		else if (is_callable(array($view, '__toString'))) {
			$result =(string) $view;
		}
		else {
			$err  = "view must be a string or an object the implements ";
			$err .= "Appfuel\View\ViewInterface or an object thtat implemnts ";
			$err .= "__toString";
			throw new DomainException($err);
		}

		return $result;
	} 

	/**
	 * @throws	DomainException
	 * @param	string	$format
	 * @return	ViewInterface
	 */
	public function createTemplate($format)
	{
		switch ($format) {
			case 'html': $template = $this->createHtmlPage();	  break;
			case 'csv' : $template = $this->createCsvTemplate();  break;
			case 'json': $template = $this->createAjaxTemplate(); break;
			case 'text': $template = $this->createViewTemplate(); break;
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
