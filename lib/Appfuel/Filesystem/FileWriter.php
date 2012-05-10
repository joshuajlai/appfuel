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
namespace Appfuel\Filesystem;

use SplFileInfo,
    LogicException,
	RunTimeException,
	InvalidArgumentException,
    RecursiveIteratorIterator,
    RecursiveDirectoryIterator;

/**
 * Reads the contents of a file into memory
 */
class FileWriter implements FileWriterInterface
{
	/**
	 * @var	FileFinderInterface 
	 */
	protected $finder = null;

    /**
     *  
     * @param	FileFinderInterface 
     * @return  FileReader
     */
    public function __construct(FileFinderInterface $finder = null)
    {
		if (null === $finder) {
			$finder = new FileFinder();
		}
		$this->setFileFinder($finder);
    }

	/**
	 * @return	FileFinderInterface
	 */
	public function getFileFinder()
	{
		return $this->finder;
	}

	/**
	 * @param	FileFinderInterface		$finder
	 * @return	FileReader
	 */
	public function setFileFinder(FileFinderInterface $finder)
	{
		$this->finder = $finder;
		return $this;
	}

	/**
	 * @param	string	$data
	 * @param	string	$path
	 * @param	int	$flags
	 * @return	int
	 */
	public function putContent($data, $path, $flags = 0)
	{
		$finder = $this->getFileFinder();
		$full = $finder->getPath($path);
		return file_put_contents($full, $data, $flags);
	}

	/**
	 * @param	string	$src
	 * @param	string	$dest
	 * @return	bool
	 */
	public function copy($src, $dest)
	{
		$finder   = $this->getFileFinder();
		$fullSrc  = $finder->getPath($src);
		$fullDest = $finder->getPath($dest);

		return copy($fullSrc, $fullDest);
	}

	/**
	 * @param	string	$path 
	 * @param	int		$mode
	 * @param	bool	$isRecursive
	 * @return	
	 */
	public function mkdir($path, $mode = null, $recursive = null)
	{
		$isRecursive = false;
		if (true === $recursive) {
			$isRecursive = true;
		}

		$finder = $this->getFileFinder();
		$full   = $finder->getPath($path);
		return mkdir($full, $mode, $isRecursive);
	}

	/**
	 * @param	string	$src
	 * @param	string	$dest
	 * @return	bool
	 */
	public function copyTree($src, $dest)
	{
        $finder = $this->getFileFinder();
		$src    = $finder->getPath($src);
		$dest   = $finder->getPath($dest);
	
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($src),
            RecursiveIteratorIterator::SELF_FIRST
        );

		if (! is_dir($dest) && false === mkdir($dest)) {
			$err = "copy tree failed: could not create root directory -($dest)";
			throw new RunTimeException($err);
		}

        foreach ($iterator as $node) {
            $srcPath  =(string)$node;
			$destPath = str_replace($src, $dest, $srcPath);
            if ($node->isDir()) {
				if (! is_dir($destPath) && false === mkdir($destPath)) {
					$err  = "copy tree failed: could not create directory at ";
					$err .= "-($destPath)";
					throw new RunTimeException($err);
				}
            }
            else {
				if (false === copy($srcPath, $destPath)) {
					$err  = "copy tree failed: could not copy -($srcPath) to ";
					$err .= "-($destPath)";
					throw new RunTimeException($err);
				}
            }
        }
		
		return true;
	}

    /**
     * Recursively delete a directory and its contents
     *
     * @param   $path
     * @return  bool
     */
    public function deleteTree($path, $leaveRoot = false)
    {
        $finder = $this->getFileFinder();
        $target = $finder->getPath($path);
        if (DIRECTORY_SEPARATOR === $target) {
            $err = 'if you want to delete the root directory do it manually';
            throw new logicexception($err);
        }

        if (! $finder->fileExists($target, false)) {
            return true;
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($target),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $node) {
            $fullpath =(string)$node;
            if ($node->isDir()) {
                rmdir($fullpath);
            }
            else {
                unlink($fullpath);
            }
        }

        if (false === $leaveRoot) {
            rmdir($target);
        }

        return true;
    }
}
