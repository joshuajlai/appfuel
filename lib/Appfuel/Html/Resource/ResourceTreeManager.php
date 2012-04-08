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
	InvalidArgumentException,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileReader,
	Appfuel\Filesystem\FileFinderInterface,
	Appfuel\Html\Resource\Yui\Yui3Factory,
	Appfuel\Html\Resource\Yui\Yui3FileStackInterface;

/**
 * Pulls info about vendors, packages, and layers out of the resource tree
 */
class ResourceTreeManager
{
	/**
	 * @var string
	 */
	static protected $defaultVendor = 'appfuel';

	/**
	 * Relative path of the resource tree file
	 * @var string
	 */
	static protected $treeFile = 'app/resource-tree.json';

	/**
	 * @var FileFinderInterface
	 */
	static protected $finder = null;
	
	/**
	 * @var array
	 */
	static protected $adapters = array();

	/**
	 * list of resource factories
	 * @var array
	 */
	static protected $factories = array();

	/**
	 * @param	PkgNameInterface	$name
	 * @param	FileStackInterface	$result
	 * @return	null
	 */
	static public function loadLayer(PkgNameInterface   $name, 
									 FileStackInterface $result)
	{
		$layer      = self::getPkg($name);
		$vendor     = $layer->getVendor();
		$vendorName = $vendor->getVendorName();
		$factory    = self::loadFactory($vendorName); 
		
		$stack = $factory->createFileStack();
		$layer->setFileStack($stack);
	
		$list = $layer->getPackages();
		foreach ($list as $pkgName) {
			if ('yui3' === $vendorName) {
				self::resolveYui($pkgName, $stack);
				$stack->sortByPriority();
			}
			else {
				self::resolve($pkgName, $stack);
			}
			
			$result->load(array(
				'js'  => $layer->getAllJsSourcePaths(),
				'css' => $layer->getAllCssSourcePaths()
			));
		}
	}

	/**
	 * @param	PkgNameInterface $name
	 * @param	FileStackInterface $result
	 * @return	null
	 */
	static public function loadPkg(PkgNameInterface $name,
								   FileStackInterface $result)
	{
		$vendorName = $name->getVendor();
		$factory    = self::loadFactory($vendorName);
		$stack      = $factory->createFileStack();
		if ('yui3' === $vendorName) {
			self::resolveYui($name, $stack);
		}
		else {
			self::resolve($name, $stack);
		}
				
		$result->loadStack($stack);
	}

	/**
	 * @param	string|PkgNameInterface	$page
	 * @param	string	$defaultVendor
	 * @return	HtmlDocPkg
	 */
	static public function resolvePage($page, FileStackInterface $result)
	{
		if (! self::isTree()) {
			self::loadTree();
		}
		$pageName = self::createPkgName($page);
		$pagePkg  = self::getPkg($pageName);

		$layers = $pagePkg->getLayers();
		foreach ($layers as $layerName) {
			self::loadLayer($layerName, $result);
		}

		$stack = new FileStack();
		self::resolve($pageName, $stack);
		$path = self::getVendorPath($pageName->getVendor());
		$result->loadStack($stack);	
		$result->load(array(
			'js'  => $pagePkg->getFiles('js', $path),
			'css' => $pagePkg->getFiles('css', $path)
		));

		$htmlName = $pagePkg->getHtmlDocName();
		return self::getPkg($pagePkg->getHtmlDocName());
	}

	/**
	 * Resolve appfuel dependencies adding each packages files to the
	 * file stack
	 *
	 * @param	PkgNameInterface	$pkgName
	 * @param	string				$path
	 * @param	FileStackInterface	$stack
	 * @return	null
	 */
	static public function resolve(PkgNameInterface $pkgName, 
								   FileStackInterface $stack)
	{
		$pkg = self::getPkg($pkgName);
		if (false === $pkg) {
            $err = "can not resolve dependecies -({$pkgName->getName()}) ";
            throw new DomainException($err);
        }

		$path = self::getVendorPath($pkgName->getVendor());
		if (false === $path) {
			$err = "can not resolve the package path for vendor ";
			$err = "-({$pkgName->getVendor()}, {$pkgName->getName()})";
			throw new DomainException($err);
		}

        if ($pkg->isRequiredPackages()) {
            $list = $pkg->getRequiredPackages();
            foreach ($list as $reqPkgName) {
                self::resolve($reqPkgName, $stack);
            }
        }

        $js  = $pkg->getFiles('js', $path);
        if (! $js) {
            $js = array();
        }

        $css = $pkg->getFiles('css', $path);
        if (! $css) {
            $css = array();
        }
	
        $stack->load(array('js' => $js, 'css' => $css));
	}

	/**
	 * Resolve appfuel dependencies adding each packages files to the
	 * file stack
	 *
	 * @param	PkgNameInterface	$pkgName
	 * @param	string				$path
	 * @param	FileStackInterface	$stack
	 * @return	null
	 */
	static public function resolveYui(PkgNameInterface $pkgName,
									  Yui3FileStackInterface $stack)
	{
		$pkg  = self::getPkg($pkgName);
		if (! $pkg) {
            $err = "can not resolve dependecies -({$pkgName->getName()}) ";
            throw new DomainException($err);
		}

		$path = self::getVendorPath($pkgName->getVendor());
		if (false === $path) {
			$err = "can not resolve the package path for vendor ";
			$err = "-({$pkgName->getVendor()}, {$pkgName->getName()})";
			throw new DomainException($err);
		}

        $name = $pkg->getName();
        $type  = 'js';
        if ($pkg->isCss()) {
            $type = 'css';
        }

        if ($pkg->hasNoDependencies()) {
            $stack->add($type, $name);
        } else if ($pkg->isUse()) {
            $list = $pkg->getUse();
            foreach ($list as $yuiName) {
                self::resolveYui($yuiName, $stack);
            }
        }
        else if ($pkg->isRequire()) {
            $list = $pkg->getRequire();
            foreach ($list as $yuiName) {
                self::resolveYui($yuiName, $stack);
            }
            $stack->add($type, $name);
        }

        if ($pkg->isAfter()) {
            $afterList = $pkg->getAfter();
            foreach ($afterList as $afterName) {
                $stack->addAfter($type, $name,  $afterName);
            }
        }
	}
									

	static public function loadVendor($name)
	{
		$vendor = ResourceTree::getVendor($name);
		if ($vendor instanceof VendorInterface) {
			return $vendor;
		}

		$factory = self::loadFactory($name);
		$vendor  = $factory->createVendor(array(
			'name'    => $name,
			'version' => ResourceTree::getVersion($name),
			'path'    => ResourceTree::getPath($name) 
		));
		ResourceTree::setVendor($name, $vendor);
		return $vendor;
	}

	static public function getPkg(PkgNameInterface $pkgName)
	{
		$vendor  = $pkgName->getVendor();
		$factory = self::loadFactory($vendor);
		$type    = $pkgName->getType();
		$name    = $pkgName->getName();

		$pkg = null;
		if ('layer' === $type) {
			$data = ResourceTree::getLayer($vendor, $name);
			if (! isset($data['pkg'])) {
				$err = "pkg must be defined for layer -($vendor, $name)";
				throw new DomainException($err);
			}

			if (! isset($data['filename'])) {
				$err = "filename must be defined for layer -($vendor, $name)";
				throw new DomainException($err);
			}
	
			$pkg = $factory->createLayer($name, self::loadVendor($vendor));
		
			$pkg->setFilename($data['filename'])
				->setPackages($data['pkg']);

		}
        else if (empty($type)) {
            $pkg = ResourceTree::getPackage($vendor, $name);
			if (is_array($pkg)) {
				$pkg = $factory->createPkg($pkg, $vendor);
				ResourceTree::setPackage($vendor, $name, $pkg);
			}
		
        }
        else {
            $pkg = ResourceTree::getPackageByType($vendor, $type, $name);
			if (is_array($pkg)) {
				$pkg = $factory->createPkg($pkg, $vendor);
				ResourceTree::setPackageByType($vendor, $type, $name, $pkg);
			}
        }

		return $pkg;
	}

	/**
	 * @return	string
	 */
	static public function getVendorPath($vendor)
	{
		return ResourceTree::getPath($vendor);
	}

	/**
	 * @return	bool
	 */
	static public function isVendor($name)
	{
		return ResourceTree::isVendorInTree($name);
	}

	/**
	 * @return string
	 */
	static public function getDefaultVendor()
	{
		return self::$defaultVendor;
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$name
	 * @return	null
	 */
	static public function setDefaultVendor($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = 'name of default vendor must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		self::$defaultVendor = $name;
	}

	/**
	 * @param	mixed	$name
	 * @param	string	$defaultVendor
	 * @return	PkgNameInterface
	 */
	static protected function createPkgName($name, $defaultVendor = null)
	{
		if ($name instanceof PkgNameInterface) {
			return $name;
		}

		if (! is_string($name) || empty($name)) {
			$err = 'package name must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		return new PkgName($name, $defaultVendor);
	}

	/**
	 * @return	FileFinderInterface
	 */
	static public function getFileFinder()
	{
		return self::$finder;
	}

	/**
	 * @param	FileFinderInterface	$finder
	 * @return	null
	 */
	static public function setFileFinder(FileFinderInterface $finder)
	{
		self::$finder = $finder;
	}

	/**
	 * @return	FileFinderInterface
	 */
	static public function loadFileFinder()
	{
		$finder = self::getFileFinder();
		if (! $finder) {
			$finder = new FileFinder();
			self::setFileFinder($finder);
		}

		return $finder;
	}

	static public function loadAdapter($vendor)
	{
		if (! is_string($vendor) || empty($vendor)) {
			$err = 'vendor must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		if (isset(self::$adapters[$vendor])) {
			return self::$adapters[$vendor];
		}

		$factory = self::loadFactory($vendor);
		$adapter = $factory->createResourceAdapter();
		self::$adapters[$vendor] = $adapter;
		return $adapter;
	}

	/**
	 * @param	string	$vendor
	 * @return	ResourceFactoryInterface
	 */
	static public function loadFactory($vendor)
	{
		if (isset(self::$factories[$vendor])) {
			return self::$factories[$vendor];
		}

		if ('yui3' === $vendor) {
			$factory = new Yui3Factory();
		}
		else {
			$factory = new ResourceFactory();
		}
		self::$factories[$vendor] = $factory;
		return $factory;
	}

	/**
	 * @return	string
	 */
	static public function getTreeFile()
	{
		return self::$treeFile;
	}

	/**
	 * @throws	InvalidArgumentException
	 * @param	string	$relativePath
	 * @return	null
	 */
	static public function setTreeFile($relativePath)
	{
		if (! is_string($relativePath) || empty($reslativePath)) {
			$err = 'path of the resource tree file must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		self::$treeFile;
	}

	/**
	 * @return	bool
	 */
	static public function isTree()
	{
		return ResourceTree::isTree();
	}

	/**
	 * @return	null
	 */
	static public function loadTree()
	{
		$file   = self::getTreeFile();
		$finder = self::loadFileFinder();
		$finder->setRootPath($file);
		$reader = new FileReader($finder);
		$data   = $reader->decodeJsonAt();
		if (empty($data)) {
			$err  = "could not load resource tree or tree is empty ";
			$err .= "-({$finder->getPath()})";
			throw new RunTimeException($err);
		}
		ResourceTree::setTree($data);

		/*
		 * we want to reuse the finder so set the root back to the base path
		 */
		$finder->setRootPath('');
	}
}
