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
namespace MyFakeApp\App\Action;

use Appfuel\App\Action\ActionBuilder as ParentActionBuilder;

/**
 * Designed only to test to how extendable appfuels action builder is. This 
 * class is used by unit tests.
 */
class ActionBuilder extends ParentActionBuilder
{
}
