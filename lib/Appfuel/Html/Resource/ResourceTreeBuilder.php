<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Html\Resource;

use DomainException,
	RunTimeException,
	InvalidArgumentException,
	RecursiveIteratorIterator,
	RecursiveDirectoryIterator,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileReader,
	Appfuel\Filesystem\FileFinderInterface;

/**
 * Build an intermediate tree (array) which holds information about vendors
 * and their packages
 */
class ResourceTreeBuilder implements ResourceTreeBuilderInterface
{
	/**
	 * Default location of the json file that describes all the vendors
	 * @var string
	 */
	protected $defaultVendorFile = 'resource/vendors.json';

	/**
	 * @param	string	$path	
	 * @param	bool	$isBasePath
	 * @return	TreeBuilder
	 */
	public function buildTree($path = null, $isBasePath = true)
	{
		if (null === $path) {
			$path = $this->getDefaultVendorFile();
		}
		else if (! is_string($path) || empty($path)) {
			$err = 'path to vendors json file must be a non empty string';
			throw new InvalidArgumentException($path);
		}

		$list = $this->createVendorList(new FileFinder($path, $isBasePath));
		return $this->processVendorList($list);
	}

	/**
	 * @param	FileFinderInterface $finder
	 * @return	ResourceTreeBuilder
	 */
	public function createVendorList(FileFinderInterface $finder)
	{
		if (! $finder->fileExists()) {
			$err  = "failed to create vendor list: file not found at ";
			$err .= "-({$finder->getPath()})";
			throw new DomainException($err);
		}
		$reader = new FileReader($finder);

		$vendors = $reader->decodeJsonAt();
		if (null === $vendors) {
			$err = "error processing json: -({$reader->getLastJsonError()} at ";
			$err .= "{$finder->getPath()})";
            throw new RunTimeException($err);
		}

		return $vendors;
	}

	/**
	 * @param	array	$vendors
	 * @return	array
	 */
	public function processVendorList(array $vendors)
	{
		$tree = array();
        foreach ($vendors as $key => $data) {
            if (! is_string($key) || empty($key)) {
                $err  = "invalid list format: vendor key must be a string";
                throw new RunTimeException($err);
            }

            $tree[$key] = $data;
            if (isset($data['tree-path']) && is_string($data['tree-path'])) {
                $list = $this->createPackageTree($data['tree-path']);
            }
            else if (isset($data['path']) && is_string($data['path'])) {
                $list = $this->discoverPackageTree($data['path']);
            }
            else {
                $err  = "could not build tree: path to package tree not found ";
                $err .= "or path to package dir not found";
                throw new RunTimeException($err);
            }
            $tree[$key]['list'] = $list;
        }

		return $tree;
	}

    /**
     * @param   string  $path
     * @return  array
     */
    public function createPackageTree($path)
    {
        $isBasePath = true;
        if ('/' === $path{0}) {
            $isBasePath = false;
        }

        $finder = new FileFinder(null, $isBasePath);
        $fileReader = new FileReader($finder);
        $data = $fileReader->decodeJsonAt($path, true);
        if (null === $data) {
            $err  = "could not decode package tree at -($path): ";
            $err .= "{$fileReader->getLastJsonError()}";
            throw new RunTimeException($err);
        }

        return $data;
    }

    /**
     * @param   string  $vendorFile
     * @return  array
     */
    public function discoverPackageTree($path)
    {
        $isBasePath = true;
        if ('/' === $path{0}) {
            $isBasePath = false;
        }

        $finder = new FileFinder('resource', $isBasePath);
        $fileReader = new FileReader($finder);

        $packages = array();
        $topDir   = new RecursiveDirectoryIterator($finder->getPath($path));
        $fileReader->setFileFinder(new FileFinder(null, false));

        foreach (new RecursiveIteratorIterator($topDir) as $file) {
            if ('json' !== $file->getExtension()) {
                continue;
            }

            $data = $fileReader->decodeJsonAt($file->getPathName(), true);
            if (null === $data) {
                $info = $file->getPathInfo();
                $dir  = $info->getFileName();
                $err  = "error parsing the manfiest json file for -($dir): ";
                $err .= "{$fileReader->getLastJsonError()}";
                throw new RunTimeException($err);
            }

            if (! isset($data['name'])) {
				$err = 'every resource pkg must have a -(name) property ';
				$err = 'defined: none found';
				throw new DomainException($err);
			}
            $name = $data['name'];
			
            if (! is_string($name) || empty($name)) {
                $err  = "property -(name) must be a none empty string ";
                $err .= "-({$file->getPathInfo()->getFileName()})";
                throw new RunTimeException($err);
            }

			if (! isset($data['type'])) {
				$err  = 'every resource pkg must have a -(type) property ';
				$err .= 'defined: none found';
				throw new DomainException($err);
			}
			$type = $data['type'];

			if (! is_string($type) || empty($type)) {
				$err  = "appfuel resource -($name) json must have a type ";
				$err .= "property as which is a non empty string ";
				throw new DomainException($err);				
			}

            if (isset($packages[$type][$name])) {
                $err = "can not build tree -($name) is already defined";
                throw new RunTimeException($err);
            }

            $packages[$type][$name] = $data;
        }

        return $packages;
    }

	/**
	 * @return	string
	 */
	public function getDefaultVendorFile()
	{
		return $this->defaultVendorFile;
	}
}
