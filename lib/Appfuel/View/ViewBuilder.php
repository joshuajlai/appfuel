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

use Appfuel\Framework\Exception,
	Appfuel\Console\ConsoleViewTemplate,
	Appfuel\Framework\View\ViewBuilderInterface;

/**
 * The view builder knows how to build views for Console, Html, Json 
 */
class ViewBuilder implements ViewBuilderInterface
{
	/**
	 * @var	ViewTemplate for a given page
	 */
	protected $pageView = null;

	/**
	 * @return	HtmlDocTemplate
	 */
	public function createHtmlTemplate()
	{
		return new HtmlDocTemplate();
	}

	/**
	 * This will look for a HtmlView class in a given namespace and 
	 * build the layout from that 
	 *
	 * @param	string	$namespace	namespace of the controller 
	 * @return	ViewCompositeTemplate
	 */
	public function buildHtmlView($namespace)
	{
		$class = "$namespace\HtmlView";
		try {
			$view = new $class();
		} catch (Exception $e) {
			throw new Exception("Could not find class $class", 0, $e);
		}

		if (! $view instanceof HtmlViewInterface) {
			throw new Exception("html view does not use correct interface");
		}

        $html = $this->createHtmlTemplate();
		$viewLabel = $view->getViewLabel();

		if ($view->belongsToLayout()) {
			$label = $html->getLayoutLabel();

			$layout = $this->buildLayout($view->getLayoutCode());
			$layout->addTemplate($viewLabel, $htmlView);
			$html->addTemplate($layoutLabel, $layout);
			return $html;
		}

		$html->addTemplate($viewLabel, $view);
		return $html;
	}

	/**
	 * @return	ConsoleViewTemplate
	 */
	public function buildConsoleView($namespace = null)
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

		if (! $view instanceof ConsoleViewInterface) {
			throw new Exception("console view does not use correct interface");
		}

		return $view;
	}

	/**
	 * @return	JsonTemplate
	 */
	public function buildServiceView()
	{
        $class = "$namespace\JsonView";
        try {
            $view = new $class();
        } catch (Exception $e) {
            $view = new JsonViewTemplate();
        }

		if (! $view instanceof JsonInterface) {
			throw new Exception("json view does not use correct interface");
		}

		return $view;
	}

	/**
	 * @return	HtmlApiTemplate
	 */
	public function buildApiView()
	{
        $class = "$namespace\ApiView";
        try {
            $view = new $class();
        } catch (Exception $e) {
            $view = new ApiViewTemplate();
        }

        if (! $view instanceof ApiViewInterface) {
            throw new Exception("api view does not use correct interface");
        }

		return $view;
	}

	/**
	 * @return	ViewFormatterInterface
	 */
	public function createView($namespace, $type = null)
	{
        if (null === $type) {
            if (! defined('AF_APP_TYPE')) {
                throw new Exception("constant AF_APP_TYPE not declared");
            }
            $type = AF_APP_TYPE;
        }

        if (! is_string($type)) {
            throw new Exception("type paramter must be a string");
        }

        /*
         * I know I don't need to add console because its the default type
         * however its listed to show all the possible types
         */
        switch($type) {
            case 'app-page':
				$view = $this->buildHtmlView($namespace);
				break;
            case 'app-api':
				$view = $this->buildApiView($namespace);
				break;
			case 'app-service':
				$view = $this->buildJsonView($namespace);
                break;
            case 'app-console':
                $view = $this->buildConsoleView($namespace);
                break;
            default:
                $view = new ViewTemplate();
        }

		return $view;
	}
}
