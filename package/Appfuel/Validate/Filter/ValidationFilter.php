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
namespace Appfuel\Validate\Filter;

use Appfuel\DataStructure\DictionaryInterface;

/**
 * Create the filter from the name given. In this case the 
 */
class ValidationFilter implements FilterInterface
{
	/**
	 * The name this filter was mapped with
	 * @var string
	 */
	protected $name = null;

	/**
	 * Dictionary of options used to control the filter's behavior
	 * @var DictionaryInterface
	 */
	protected $options = null;

	/**
	 * Message used when this filter fails
	 * @var string
	 */
	protected $error = null;

}
