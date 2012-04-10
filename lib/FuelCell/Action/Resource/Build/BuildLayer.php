<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace FuelCell\Action\Resource\Build;

use Exception,
	RunTimeException,
	Appfuel\Filesystem\FileFinder,
    Appfuel\Filesystem\FileWriter,
    Appfuel\Filesystem\FileReader,
    Appfuel\Html\Resource\PkgName,
	Appfuel\Html\Resource\PkgNameInterface,
    Appfuel\Html\Resource\FileStack,
	Appfuel\Html\Resource\FileStackInterface,
    Appfuel\Html\Resource\ContentStack,
    Appfuel\Html\Resource\ResourceTreeManager,
	Appfuel\Html\Resource\ResourceLayerInterface,
	Appfuel\Kernel\Mvc\MvcAction,
	Appfuel\Kernel\Mvc\MvcContextInterface;

class BuildLayer extends MvcAction
{
	/**
	 * @param	MvcContextInterface $context
	 * @return	null
	 */
	public function process(MvcContextInterface $context)
	{
		$input = $context->getInput();

		if (! ResourceTreeManager::isTree()) {
			ResourceTreeManager::loadTree();
		}

		if (true ===(bool) $input->get('get', 'all', false)) {
			$this->buildAllPages();
			return;
		}

		$page = $input->get('get', 'page');
		if ($page) {
			$pkgName = $this->createPkgName($page, 'page failed: ');
			$this->buildPage($pkgName);
		}

		$view = $context->getView();
		$view->assign('result', 'build completed');
	}

	/**
	 * @return	null
	 */
	protected function buildAllLayers()
	{
		$list = ResourceTreeManager::getAllLayerNames();
		foreach ($list as $name) {
			$this->buildLayerByName($name);
		}
	}

	protected function buildLayerByName(PkgNameInterface $name,
										FileStackInterface $pageStack)
	{
		$stack = new FileStack();
		$layer = ResourceTreeManager::loadLayer($name, $stack);

		return $this->buildLayer($layer, $pageStack);
	}

	/**
	 * @param	ResourceLayerInterface $layer
	 * @return	int
	 */
	protected function buildLayer(ResourceLayerInterface $layer, 
								  FileStackInterface $pageStack)
	{
		$stack        = $layer->getFileStack();
		$vendor		  = $layer->getVendor(); 
		$buildDir     = $layer->getBuildDir();
		$buildFile    = $layer->getBuildFile();
		$jsBuildFile  = "$buildFile.js";
		$cssBuildFile = "$buildFile.css";

		$finder = new FileFinder('resource');
		$reader = new FileReader($finder);

		$path    = $vendor->getPackagePath();	
		

		if ($layer->isJs()) {
			$jsList  = $pageStack->diff('js', $layer->getAllJsSourcePaths());
			$content = new ContentStack();
			foreach ($jsList as $file) {
				$text = $reader->getContent($file);
				if (false === $text) {
					$err = "could not read contents of file -($file)";
					throw new RunTimeException($err);
				}
				$content->add($text);
				$pageStack->add('js', $file);
			}
	
			$writer = new FileWriter($finder);
			if (! $finder->isDir($buildDir)) {
				if (! $writer->mkdir($buildDir, 0755, true)) {
					$path = $finder->getPath($buildDir);
					$err = "could not create dir at -({$path})";
					throw new RunTimeException($err);
				}
			}

			if ($content->count() > 0) {
				$result = '';
				foreach ($content as $data) {
					$result .= $data . PHP_EOL;
				}

				$writer->putContent($result, $jsBuildFile);
			}
		}

		if ($layer->isCss()) {
			$cssList  = $pageStack->diff('css', $layer->getAllCssSourcePaths());
			$content = new ContentStack();
			foreach ($cssList as $file) {
				$text = $reader->getContent($file);
				if (false === $text) {
					$err = "could not read contents of file -($file)";
					throw new RunTimeException($err);
				}
				$content->add($text);
				$pageStack->add('css', $file);
			}
		
			if ($content->count() > 0) {
				$result = '';
				foreach ($content as $data) {
					$result .= $data . PHP_EOL;
				}

				$writer->putContent($result, $cssBuildFile);
			}
		}
	}

	/**
	 * @param	ThemePkgInterface $theme
	 * @return	null
	 */
	protected function buildTheme(PkgNameInterface $themePkgName)
	{
		$themeName  = $themePkgName->getName();
		$vendorName = $themePkgName->getVendor();
		$vendor     = ResourceTreeManager::loadVendor($vendorName);
		$version    = $vendor->getVersion();
		$pkgPath    = $vendor->getPackagePath();
		$buildDir   = "build/$vendorName/$version/theme/$themeName";
		$pkg        = ResourceTreeManager::getPkg($themePkgName);

		$finder = new FileFinder('resource');
		$reader = new FileReader($finder);

		$cssBuildFile = "$buildDir/$themeName.css";
		if ($pkg->isCssFiles()) {
			$cssList = $pkg->getCssFiles($pkgPath);
			$content = new ContentStack();
			foreach ($cssList as $file) {
				$text = $reader->getContent($file);
				if (false === $text) {
					$err = "could not read contents of file -($file)";
					throw new RunTimeException($err);
				}
				$content->add($text);
			}

			$writer = new FileWriter($finder);
			if (! $finder->isDir($buildDir)) {
				if (! $writer->mkdir($buildDir, 0755, true)) {
					$path = $finder->getPath($buildDir);
					$err = "could not create dir at -({$path})";
					throw new RunTimeException($err);
				}
			}


			if ($content->count() > 0) {
				$result = '';
				foreach ($content as $data) {
					$result .= $data . PHP_EOL;
				}

				$writer->putContent($result, $cssBuildFile);
			}
		}

		if ($pkg->isAssetFiles()) {
			$assetList = $pkg->getAssetFiles();
			foreach ($assetList as $file) {
				$src  = "$pkgPath/$file";
				$dest = "build/$vendorName/$version/$file";
				$writer->copy($src, $dest);
			}
		}
	}

	/**
	 * @return	null;
	 */
	protected function buildAllPages()
	{
		$list = ResourceTreeManager::getAllPageNames();
		if (! is_array($list) || empty($list)) {
			return;
		}

		foreach ($list as $pageName) {
			$this->buildPage($pageName);
		}
	}

	/**
	 * @param	string	$page
	 * @return	null
	 */
	protected function buildPage(PkgNameInterface $pageName)
	{
		$pkg    = ResourceTreeManager::getPkg($pageName);
		$layers = $pkg->getLayers(); 
		$pageStack = new FileStack();
		foreach ($layers as $layerName) {
			$this->buildLayerByName($layerName, $pageStack);
		}

		$layer = ResourceTreeManager::createPageLayer($pageName);
		$this->buildLayer($layer, $pageStack);

		$themeName = $pkg->getThemeName();
		if ($themeName) {
			$this->buildTheme($themeName);
		}
	}

	/**
	 * @param	string	$name
	 * @param	string	$msg
	 * @return	PkgName
	 */
	protected function createPkgName($name, $msg)
	{
		try {
			$pkgName = new PkgName($name);
		} catch (Exception $e) {
			$err = $msg . $e->getMessage();
			throw new RunTimeException($err);
		}

		return $pkgName;
	}
}
