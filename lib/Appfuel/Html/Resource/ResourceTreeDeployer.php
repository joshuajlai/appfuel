<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Html\Resource;

use Exception,
	RunTimeException,
	Appfuel\Filesystem\FileFinder,
    Appfuel\Filesystem\FileWriter,
    Appfuel\Filesystem\FileReader,
    Appfuel\Filesystem\FileReaderInterface,
    Appfuel\Filesystem\FileWriterInterface;

/**
 * Build javascript and css into concatenated files. Also move theme packages
 * to their build locations
 */
class ResourceTreeDeployer
{
	/**
	 * @param	MvcContextInterface $context
	 * @return	null
	 */
	public function deploy()
	{
		if (! ResourceTreeManager::isTree()) {
			ResourceTreeManager::loadTree();
		}

 		$finder = new FileFinder('resource');
		$writer = new FileWriter($finder);
		$reader = new FileReader($finder);
        if (! $writer->deleteTree('build', true)) {
            $err = "could not delete -({$finder->getPath('build')})";
            throw new RunTimeException($err);
        }

 		$list = ResourceTreeManager::getAllPageNames();
		if (! is_array($list) || empty($list)) {
			return;
		}

		foreach ($list as $pageName) {
		    $pkg = ResourceTreeManager::getPkg($pageName);
		    $layers = $pkg->getLayers(); 
		    $pageStack = new FileStack();
		    foreach ($layers as $layerName) {
                $stack = new FileStack();
		        $layer = ResourceTreeManager::loadLayer($layerName, $stack);
			    $this->buildLayer($layer, $reader, $writer, $pageStack);
		    }

		    $this->buildLayer(
                ResourceTreeManager::createPageLayer($pageName),
                $reader,
                $writer,
                $pageStack
            );

		    $themeName = $pkg->getThemeName();
		    if ($themeName) {
			    $this->buildTheme($themeName, $reader, $writer, $pageStack);
		    }
		}

		return true;
	}

	/**
	 * @param	ResourceLayerInterface $layer
	 * @return	int
	 */
	protected function buildLayer(ResourceLayerInterface $layer, 
                                  FileReaderInterface $reader,
                                  FileWriterInterface $writer,
								  FileStackInterface $pageStack)
	{
		$stack        = $layer->getFileStack();
		$vendor		  = $layer->getVendor(); 
		$buildDir     = $layer->getBuildDir();
		$buildFile    = $layer->getBuildFile();
		$jsBuildFile  = "$buildFile.js";
		$cssBuildFile = "$buildFile.css";
        $finder       = $reader->getFileFinder();
	
        if (! $finder->isDir($buildDir)) {
            if (! $writer->mkdir($buildDir, 0755, true)) {
                $path = $finder->getPath($buildDir);
                $err = "could not create dir at -({$path})";
                throw new RunTimeException($err);
            }
        }

		if ($layer->isJs()) {
			$list   = $pageStack->diff('js', $layer->getAllJsSourcePaths());
            $result = $this->makeString('js', $list, $reader, $pageStack);	
			$writer->putContent($result, $jsBuildFile);
		}

		if ($layer->isCss()) {
			$list   = $pageStack->diff('css', $layer->getAllCssSourcePaths());
            $result = $this->makeString('css', $list, $reader, $pageStack);
		    $writer->putContent($result, $cssBuildFile);
		}
	}

	/**
	 * @param	ThemePkgInterface $theme
	 * @return	null
	 */
	protected function buildTheme(PkgNameInterface $themePkgName,
                                  FileReaderInterface $reader,
                                  FileWriterInterface $writer,
                                  FileStackInterface $pageStack)
	{
		$themeName  = $themePkgName->getName();
		$vendorName = $themePkgName->getVendor();
		$vendor     = ResourceTreeManager::loadVendor($vendorName);
		$version    = $vendor->getVersion();
		$pkgPath    = $vendor->getPackagePath();
        $buildDir   = "build/$vendorName/$version";
		$themeDir   = "$buildDir/theme/$themeName/css";
		$pkg        = ResourceTreeManager::getPkg($themePkgName);
        $finder     = $reader->getFileFinder();

        if (! $finder->isDir($themeDir)) {
            if (! $writer->mkdir($themeDir, 0755, true)) {
                $path = $finder->getPath($themeDir);
                $err = "could not create dir at -({$path})";
                throw new RunTimeException($err);
            }
        }

		if ($pkg->isCssFiles()) {
			$list   = $pkg->getCssFiles($pkgPath);
            $result = $this->makeString('css', $list, $reader, $pageStack);	
			$writer->putContent($result, "$themeDir/$themeName.css");
		}

		if ($pkg->isAssetFiles()) {
			$list = $pkg->getAssetFiles();
            $assetBuildDir = "$buildDir/{$pkg->getAssetDir()}";
            if (! $finder->isDir($assetBuildDir)) {
                if (! $writer->mkdir($assetBuildDir, 0755, true)) {
                    $path = $finder->getPath($assetBuildDir);
                    $err = "could not create dir at -({$path})";
                    throw new RunTimeException($err);
                }
            }

			foreach ($list as $file) {
                $themeDir = "$pkgPath/{$pkg->getPath()}";
				$src  = "$pkgPath/$file";
				$dest = "$buildDir/$file";
				$result = $writer->copy($src, $dest);
			}
		}
	}


    protected function makeString($type,
                                  array $list,
                                  FileReaderInterface $reader, 
                                  FileStackInterface $pageStack)
    {

        if ('css' !== $type && 'js' !== $type) {
            $err = 'can only convert js or css files';
            throw new LogicException($err);
        }

        $content = new ContentStack();
        foreach ($list as $file) {
            $text = $reader->getContent($file);
            if (false === $text) {
                $err = "could not read contents of file -($file)";
                throw new RunTimeException($err);
            }
				
            $content->add($text);
			$pageStack->add($type, $file);
        }

        $result = '';
        if ($content->count() > 0) {
            foreach ($content as $data) {
                $result .= $data . PHP_EOL;
            }
        }

        return $result;
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
