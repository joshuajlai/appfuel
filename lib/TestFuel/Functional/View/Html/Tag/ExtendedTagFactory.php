<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace TestFuel\Functional\View\Html\Tag;

use Appfuel\View\Html\Tag\HtmlTagFactory; 

/**
 * Used in unit test for the Appfuel\View\Html\HtmlPageBuilder to prove
 * the builder can infact create this class and check its interface if its 
 * class name is added to the html page detail. 
 */
class ExtendedTagFactory extends HtmlTagFactory
{}
