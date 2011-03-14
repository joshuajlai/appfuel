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
namespace Appfuel\Framework\App;

/**
 * Used to describe the methods needed in the factory to create 
 * the necessary objects used in starup, bootstrapping, dispatching and
 * Output rendering
 */
interface InitializeInterface
{
	public function initialize($file);
}
