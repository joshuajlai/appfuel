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
namespace Test\Appfuel\App\MyRootAction;

use Appfuel\Framework\App\Action\ActionBuilderInterface;

/**
 * The purpose of this class is to simulate a different implementation of the
 * the action builder. This allows testing of the front controllers ability
 * to check for the existence of an ActionBuilder class in different namespaces
 */
class ActionBuilder implements ActionBuilderInterface
{
}

