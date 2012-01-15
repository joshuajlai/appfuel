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
namespace Appfuel\App\Resource;

use InvalidArgumentException,
	Appfuel\Kernel\PathFinderInterface;

/**
 * The resource module is a value object holding details of the module
 */
class ResourceModule implements ResourceModuleInterface
{
	/**
	 * Name of the module
	 * @var string
	 */
	protected $name = null;

	/**
	 * Type of files this module holds, css or js
	 * @var string
	 */
	protected $type = 'js';

	/**
	 * Flag used to determine if the module contains any theme resources
	 * @var bool
	 */
	protected $isTheme = false;

	/**
	 * List of modules this module depends on
	 * @var array
	 */	
	protected $depends = array();

	/**
	 * List of support languages
	 * @var array
	 */
	protected $lang = array();

	/**
	 * List of modules that should be listed after this module
	 * @var array
	 */
	protected $after = array();

	/**
	 * List of modules that should be removed because they are contained 
	 * in this module
	 * @var array
	 */
	protected $supersedes = array();

	public function __construct(array $data)
	{
		if (empty($data)) {
			$err = 'module data can not be empty';
			throw new InvalidArgumentException($err);
		}

		if (! isset($data['name']) || ! is_string($data['name'])) {
			$err = 'module name not found in module data with key -(name)';
			throw new InvalidArgumentException($err);
		}
		$this->setName($data['name']);

		if (isset($data['type']) && 'css' === $data['type']) {
			$this->type = 'css';
		}

		if (isset($data['skinnable']) && true === $data['skinnable']) {
			$this->isSkin = true;
		}
		
	}
}
