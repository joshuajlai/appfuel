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
namespace Appfuel\View\Compositor;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Filesystem\FileReader,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileFinderInterface;

/**
 * Handles interacting with the with filesystem
 */
class ViewFileManager
{
	/**
	 * @var string
	 */
	static private $reader = null;

	/**
	 * Used to resolve relative file paths to absolute paths so the
	 * formatter does not need to care
	 * @var PathFinder
	 */
	private $pathFinder = null;

    /**
     * Include Template
     * Uses output buffering to store the content and return it. The 
     * incoming parameter are obtained via func_get_arg to prevent 
     * the variable becoming visible in the template scope
     *
     * @param   string  arguement 0     file path to template
     * @return  string
     */
	static public function getTemplateBinder($path)
	{
        $reader = self::$reader;
        if (! $reader) {
            $finder = new FileFinder('resources');
            $reader = new FileReader($finder);
            self::$reader = $reader;
        }

		$binder = function () use (FileReaderInterface $reader, $path) {
			ob_start();
			$reader->import($path);
			$contents = ob_get_contents();
			ob_end_clean();
			return trim($contents, " \n");
		};

		return $binder;
	}
}
