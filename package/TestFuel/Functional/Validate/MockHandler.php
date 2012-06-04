<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Testfuel\Functional\Validate;

use Appfuel\Validate\ValidationHandler;

/**
 * Used to test the ValidationFactory can map and create objects that
 * implement the correct interfaces that are not appfuel's.
 */
class MockHandler extends ValidationHandler 
{}
