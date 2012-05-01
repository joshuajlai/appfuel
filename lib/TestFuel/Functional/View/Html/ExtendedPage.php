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
namespace TestFuel\Functional\View\Html;

use Appfuel\View\Html\HtmlPage,
	Appfuel\View\Html\HtmlPageInterface;

/**
 * Used to in unit tests to test code that dynamically builds page classes
 * from a string
 */
class ExtendedPage extends HtmlPage
{}
