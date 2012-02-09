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

use Appfuel\View\AjaxTemplate,
	Appfuel\View\ViewTemplate,
	Appfuel\View\ViewInterface,
	Appfuel\View\FileViewTemplate,
	Appfuel\View\Html\HtmlTemplate,
	Appfuel\Console\ConsoleTemplate,
    Appfuel\View\Html\HtmlPageBuilder,
    Appfuel\View\Html\HtmlPageBuilderInterface,
	Appfuel\ClassLoader\StandardAutoLoader,
	Appfuel\ClassLoader\AutoLoaderInterface;

/**
 * Used to build action controllers
 */
interface MvcViewBuilderInterface
{
    /**
     * @return  AutoLoaderInterface
     */
    public function getClassLoader();

    /**
     * @param   AutoLoaderInterface $loader
     * @return  MvcActionBuilder
     */
    public function setClassLoader(AutoLoaderInterface $loader);

    /**
     * @return  HtmlPageBuilderInterface
     */
    public function getHtmlPageBuilder();

    /**
     * @param   HtmlPageBuilderInterface
     * @return  MvcContextBuilder
     */
    //public function setHtmlPageBuilder(HtmlPageBuilderInterface $builder);


}
