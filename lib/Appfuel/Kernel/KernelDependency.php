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
 */
class KernelDependency extends ClassDependency
{
	/**
	 * List of namespaces to be mapped to file paths
	 * @var array
	 */
	protected $map = array(
		'\Appfuel\Error\ErrorInterface' => 'Appfuel/Error/ErrorInterface.php',
		'\Appfuel\Error\ErrorStackInterface' => 'Appfuel/Error/ErrorStackInterface.php',
		'\Appfuel\Error\ErrorItem' => 'Appfuel/Error/ErrorItem.php',
		'\Appfuel\Error\ErrorStack' => 'Appfuel/Error/ErrorStack.php',
		'\Appfuel\DataStructure\DictionaryInterface' => 'Appfuel/DataStructure/DictionaryInterface.php',
		'\Appfuel\DataStructure\Dictionary' => 'Appfuel/DataStructure/Dictionary.php',
		'\Appfuel\Console\ConsoleOutputInterface' => 'Appfuel/Console/ConsoleOutputInterface.php',
		'\Appfuel\Console\ConsoleOutput' => 'Appfuel/Console/ConsoleOutput.php',
		'\Appfuel\Http\HttpOutputInterface' => 'Appfuel/Http/HttpOutputInterface.php',
		'\Appfuel\Http\HttpOutput' => 'Appfuel/Http/HttpOutput.php',
		'\Appfuel\Kernel\FaultHandlerInterface' => 'Appfuel\Kernel\FaultHandlerInterface',
		'\Appfuel\Kernel\FaultHandler' => 'Appfuel\Kernel\FaultHandler',
		'\Appfuel\Kernel\Error\ErrorLevelInterface',
		'\Appfuel\Kernel\Error\ErrorLevel',
		'\Appfuel\Kernel\Error\ErrorDisplayInterface',
		'\Appfuel\Kernel\Error\ErrorDisplay',
		'\Appfuel\Kernel\IncludePathInterface',
		'\Appfuel\Kernel\IncludePath',
		'\Appfuel\Kernel\Startup\ConfigRegistry',
		'\Appfuel\Kernel\Startup\ConfigLoader',
		'\Appfuel\Kernel\Startup\StartupTaskInterface',
		'\Appfuel\Kernel\Startup\StartupTaskAbstract',
		'\Appfuel\Log\LogPriorityInterface',
		'\Appfuel\Log\LogEntryInterface',
		'\Appfuel\Log\LogAdapterInterface',
		'\Appfuel\Log\LoggerInterface',
		'\Appfuel\Log\SysLogAdapter',
		'\Appfuel\Log\LogEntry',
		'\Appfuel\Log\LogPriority',
		'\Appfuel\Log\Logger',
	);
}
