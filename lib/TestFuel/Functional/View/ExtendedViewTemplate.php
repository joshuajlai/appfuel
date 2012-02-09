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
namespace TestFuel\Functional\View;

use Appfuel\View\ViewInterface; 

/**
 * Used in any unit test that needs check code that will dynamically create
 * a view template from a string that is the class name
 */
class ExtendedViewTemplate extends ViewInterface
{}
