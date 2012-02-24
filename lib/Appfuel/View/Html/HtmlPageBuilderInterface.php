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

use Appfuel\View\ViewInterface,
	Appfuel\Kernel\PathFinder,
	Appfuel\Kernel\PathFinderInterface,
	Appfuel\View\Html\Tag\HtmlTagFactoryInterface;

/**
 * Builds and configures an html page using an HtmlPageDetailInterface
 */
interface HtmlPageBuilderInterface
{
	/**
	 * @return	PathFinderInterface
	 */
	public function getPathFinder();

	/**
	 * @return	HtmlPageConfigurationInterface
	 */
	public function getPageConfiguration();

    /**
     * @throws  InvalidArgumentException
     * @throws  RunTimeException
     * @param   string  $filePath
     * @return  array
     */
    public function getConfigurationData($path);

	/**
	 * @param	HtmlPageDetailInterface $detail
	 * @return	HtmlPageInterface
	 */
	public function buildPage($detail);

    /**
     * @param   HtmlTagFactoryInterface $factory
     * @param   ViewInterface $view
	 * @param	HtmlTagFactoryInterface $factory
     * @return  HtmlPage
     */
    public function createHtmlPage(HtmlPageDetailInterface $detail,
                               ViewInterface $view,
                               HtmlTagFactoryInterface $factory = null);
}
