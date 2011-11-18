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
namespace Appfuel\Kernel;

use RuntimeException,
	Appfuel\ClassLoader\ClassDependency;

/**
 * Used to declare a group of files or namespaces that should be loaded by a 
 * dependency loader which is not a registered autoloader. 
 */
class KernelDependency extends ClassDependency
{
	/**
	 * This path will be attatched to each namespace when resolving it into
	 * an absolute path.
	 * @var string
	 */
	protected $rootPath = null;

	/**
	 * List of namespaces to be mapped to file paths
	 * @var array
	 */
	protected $namespaces = array();

	/**
	 * List of files to include. This is needed when the file has more than
	 * one namespace or does not follow namespacing rules
	 * @var array
	 */
	protected $files = array();

	/**
	 * @param	string	$rootPath
	 * @return	ClassDependency
	 */
	public function __construct($rootPath = null)
	{
		if (! defined('AF_BASE_PATH')) {
			$err  = 'constant AF_BASE_PATH must be defined before loading ';
			$err .= 'dependencies';
			throw new RunTimeException($err);
		}

		$root = AF_BASE_PATH . "/lib";
		parent::__construct($root);
		
		$dependencies = array(
			'\Appfuel\Exception\AppfuelExceptionInterface',
			'\Appfuel\Exception\AppfuelException',
			'\Appfuel\Error\ErrorInterface',
			'\Appfuel\Error\ErrorStackInterface',
			'\Appfuel\Error\AppfuelError',
			'\Appfuel\Error\ErrorStack',
			'\Appfuel\Log\LogPriorityInterface',
			'\Appfuel\Log\LogEntryInterface',
			'\Appfuel\Log\LogAdapterInterface',
			'\Appfuel\Log\LoggerInterface',
			'\Appfuel\Log\SysLogAdapter',
			'\Appfuel\Log\LogEntry',
			'\Appfuel\Log\LogPriority',
			'\Appfuel\Log\Logger',
			'\Appfuel\Output\OutputAdapterInterface',
			'\Appfuel\Output\OutputEngineInterface',
			'\Appfuel\Output\OutputEngine',
			'\Appfuel\Console\ConsoleOutputAdapter',
			'\Appfuel\DataStructure\DictionaryInterface',
			'\Appfuel\DataStructure\Dictionary',
			'\Appfuel\Kernel\KernelRegistry',
			'\Appfuel\Kernel\Error\ErrorLevelInterface',
			'\Appfuel\Kernel\Error\ErrorLevel',
			'\Appfuel\Kernel\Error\ErrorDisplayInterface',
			'\Appfuel\Kernel\Error\ErrorDisplay',
			'\Appfuel\Kernel\IncludePathInterface',
			'\Appfuel\Kernel\IncludePath',
			'\Appfuel\Kernel\KernelOutput',
			'\Appfuel\Kernel\KernelRegistry',
			'\Appfuel\Kernel\Startup\StartupTaskInterface',
			'\Appfuel\Kernel\Startup\StartupTaskAbstract',
			'\Appfuel\Kernel\FaultHandlerInterface',
			'\Appfuel\Kernel\FaultHandler',
		);
		$this->loadNamespaces($dependencies);
	}
}
