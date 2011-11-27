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
namespace TestFuel\Fake\Action\TestDispatch\BadViews;

/**
 * A console view must follow the ConsoleViewTemplateInterface but does not.
 * This is done to trigger the exception for
 * Appfuel\Kernel\Mvc\ActionFactory::createConsoleView
 */
class ConsoleView
{
}
