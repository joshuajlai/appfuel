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
namespace Appfuel\View\Html;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\View\ViewInterface,
	Appfuel\View\FileViewTemplate,
	Appfuel\Kernel\PathFinder,
	Appfuel\Kernel\PathFinderInterface,
	Appfuel\View\Html\Tag\HtmlTagFactoryInterface;

/**
 * Builds and configures an html page using an HtmlPageDetailInterface
 */
class HtmlPageBuilder implements HtmlPageBuilderInterface
{
	/**
	 * Used to locate supporting files not in the include path 
	 * @var PathFinderInterface
	 */
	protected $finder = null;

	/**
	 * @var HtmlPageConfigurationInterface 
	 */
	protected $configuration = null;

	/**
	 * @param	PathFinderInterface		$finder
	 * @return	HtmlPageBuilder
	 */
	public function __construct(PathFinderInterface $finder = null,
								HtmlPageConfigurationInterface $config = null)
	{
		if (null === $finder) {
			$finder = new PathFinder('resource');
		}
		$this->finder = $finder;

		if (null === $config) {
			$config = new HtmlPageConfiguration();
		}

		$this->configuration = $config;
	}

	/**
	 * @return	PathFinderInterface
	 */
	public function getPathFinder()
	{
		return $this->finder;
	}

	/**
	 * @return	HtmlPageConfigurationInterface
	 */
	public function getPageConfiguration()
	{
		return $this->configuration;
	}

	/**
	 * @param	HtmlPageDetailInterface $detail
	 * @return	HtmlPageInterface
	 */
	public function buildPage($detail)
	{
		if (is_array($detail)) {
			$detail = $this->createHtmlPageDetail($detail);
		}
		else if (! $detail instanceof HtmlPageDetailInterface) {
			$err  = 'failed to build page: $detail must be an array or an ';
			$err .= 'object that implements Appfuel\View\Html\HtmlPage';
			$err .= 'DetailInterface';
			throw new InvalidArgumentException($err);
		}

		$tagFactory = null;
		if ($detail->isTagFactory()) {
			$tagFactory = $this->createTagFactory($detail);
		}

		if (! $detail->isViewTemplate()) {
			$err  = 'view template is required and missing from ';
			$err .= 'the html page detail';
			throw new RunTimeException($err);
		}
		$view = $this->createViewTemplate($detail);
		
		if ($detail->isLayoutTemplate()) {
			$layout = $this->createLayoutTemplate($detail, $view);
			$view = $layout;
		}
		$page = $this->createHtmlPage($detail, $view, $tagFactory);

		$jsTemplate = null;
		if ($detail->isInlineJsTemplate()) {
			$page->setInlineJsTemplate($detail->getInlineJsTemplate());
		}

		if ($detail->isHtmlDoc()) {
			$page->setHtmlDoc($detail->getHtmlDoc());
		}	

		if ($detail->isHtmlConfig()) {
			$this->configure($detail, $page);
		}

		return $page;
	}

	/**
	 * @param	HtmlPageDetailInterface	 $detail
	 * @return	HtmlTagFactoryInterface
	 */
	public function createTagFactory(HtmlPageDetailInterface $detail)
	{
		$factory = $detail->getTagFactory();
		if (is_string($factory)) {
			$factory = new $factory();
		}
		else if (! $factory instanceof HtmlTagFactoryInterface) {
			$err  = 'tag factory was defined in the html page detail ';
			$err .= 'but does not implment Appfuel\View\Html\Tag\Tag';
			$err .= 'FactoryInterface';
			throw new RunTimeException($err);
		}

		return $factory;
	}

	/**
	 * @param	HtmlPageDetailInterface $detail
	 * @return	ViewInterface
	 */
	public function createViewTemplate(HtmlPageDetailInterface $detail)
	{
		$view = $detail->getViewTemplate();
		if (is_string($view)) {
			$view = new FileViewTemplate($view);
		}
		else if (! $view instanceof ViewInterface) {
			$err  = 'view template must be a string (path to tpl) or an ';
			$err .= 'object that implments Appfuel\View\ViewInterface';
			throw new RunTimeException($err);
		}
		
		return $view;
	}

	/**
	 * @param	HtmlPageDetailInterface $detail
	 * @param	ViewInterface $view
	 * @return	HtmlLayoutInterface 
	 */
	public function createLayoutTemplate(HtmlPageDetailInterface $detail,
										 ViewInterface $view)
	{
		$layout = null;
		$tmp    = $detail->getLayoutTemplate();
		if (is_string($tmp)) {
			$layout = new $tmp();
		}
		else if (is_object($tmp)) {
			$layout = $tmp;
		}

		if (! $layout instanceof HtmlLayoutInterface) {
				$err  = 'layout class or object in the html page detail ';
				$err .= 'does not implment the Appfuel\View\Html\HtmlLayout';
				$err .= 'Interface';
				throw new RunTimeException($err);
		}
				
		if (! $layout->isViewTemplate()) {
			$layout->setView($view);
		}

		return $layout;
	}

	/**
	 * @param	$view
	 * @param	HtmlTagFactoryInterface $factory
	 * @return	HtmlPage
	 */
	public function createHtmlPage(HtmlPageDetailInterface $detail, 
							   ViewInterface $view, 
							   HtmlTagFactoryInterface $factory = null)
	{
		if ($detail->isHtmlPageClass()) {
			$pageClass = $detail->getHtmlPageClass();
			$page = new $pageClass($view, $factory);
			if (! $page instanceof HtmlPageInterface) {
				$err  = 'page class must implment Appfuel\View\Html\HtmlPage';
				$err .= 'Interface';
				throw new RunTimeException($err);
			}
		}
		else {
			$page = new HtmlPage($view, $factory);
		}

		return $page;
	}

	/**
	 * @param	array	$data
	 * @return	HtmlPageDetail
	 */
	public function createHtmlPageDetail(array $data)
	{
		return new HtmlPageDetail($data);
	}

	/**
	 * @param	HtmlPageDetailInterface $detail
	 * @param	HtmlPageInterface		$page
	 * @return	null
	 */
	public function configure(HtmlPageDetailInterface $detail, 
							  HtmlPageInterface $page)
	{
		$data = $detail->getHtmlConfig();
		if (is_string($data)) {
			$data = $this->getConfigurationData($data);
		}
		else if (! is_array($data)) {
			$err  = 'html config was definded in the html page detail but ';
			$err .= 'was not a string (path to config) or an array';
			throw new RunTimeException($err);
		}

		$configuration = $this->getPageConfiguration();
		$configuration->apply($data, $page);
	}

    /**
     * @throws  InvalidArgumentException
     * @throws  RunTimeException
     * @param   string  $filePath
     * @return  array
     */
    public function getConfigurationData($path)
    {
        if (! is_string($path) || empty($path)) {
            $err = 'file path to configuration must be a non empty array';
            throw new InvalidArgumentException($err);
        }

        return $this->getPathFinder()
					->requireFile($path);
    }
}
