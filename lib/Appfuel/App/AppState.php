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
namespace Appfuel\App;

use Appfuel\Framework\Exception;

/**
 * Describes the current state of the application
 */
class AppState
{
	/**
	 * State of the php env like error reporting, error display, default
	 * timezone, autoload function stack etc..
	 * @var array
	 */
	protected $env = array();

	/**
	 * List of data stored in the application registry
	 * @var array
	 */
	protected $registryData = array();

	/**
	 * List of the names of the initializer classes that were run
	 * @var array
	 */
	protected $init = array();

	/**
	 * List of prefilters that were run
	 * @var array
	 */
	protected $prefilters = array();

	/**
	 * List of post filters that were run
	 * @var array
	 */	
	protected $postFilters = array();

	/**
	 * Action Controller that was executed and context that was used
	 * @var array
	 */
	protected $action = array(
		'class'		=> null,
		'context'	=> null
	);

	/**
	 * List of errors that occured during the app lifecycle
	 * @var array
	 */
	protected $errors = array();

	/**
	 * @param	string	$name	name of the init as it appears in the ini
	 * @param	string	$class	name of the class it resolved too
	 * @return	AppState
	 */
	public function markInit($name, $class)
	{

	}

	
}
