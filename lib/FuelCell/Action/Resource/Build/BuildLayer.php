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

use Appfuel\Filesystem\FileFinder,
    Appfuel\Filesystem\FileWriter,
    Appfuel\Filesystem\FileReader,
    Appfuel\Html\Resource\PkgName,
    Appfuel\Html\Resource\FileStack,
    Appfuel\Html\Resource\ContentStack,
    Appfuel\Html\Resource\ResourceTreeManager,
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
			$this->buildAll();
			return;
		}

		if (true === (bool) $input->get('get', 'all-layers', false)) {
			$this->buildAllLayers();
		}

		$layer = $input->get('get', 'layer');
		if ($layer) {
			$this->buildLayer($layer);
		}

		if (true ===(bool) $input->get('get', 'all-pages', false)) {
			$this->buildAllPages();
		}

		$page = $input->get('get', 'page');
		if ($page) {
			$this->buildPage($page);
		}
		echo "\n", print_r($input, 1), "\n";exit;
	}

	/**
	 * @return	null
	 */
	protected function buildAll()
	{

	}

	/**
	 * @return	null
	 */
	protected function buildAllLayers()
	{

	}

	/**
	 * @param	string	$layer
	 * @return	null
	 */
	protected function buildLayer($name)
	{
		$name    = new PkgName($name);
		$stack   = new FileStack();
		$content = new ContentStack();
		$layer   = ResourceTreeManager::loadLayer($name, $stack);

		$buildDir     = $layer->getBuildDir();
		$buildFile    = $layer->getBuildFile();
		$jsBuildFile  = "$buildFile.js";
		$cssBuildFile = "$buildFile..css";

		$finder = new FileFinder('resource');
		$reader = new FileReader($finder);
		
		$jsList = $stack->get('js');
		foreach ($jsList as $file) {
			$text = $reader->getContent($file);
			if (false === $text) {
				$err = "could not read contents of file -($file)";
				throw new RunTimeException($err);
			}
			$content->add($text);
		}

		$result = '';
		foreach ($content as $data) {
			$result .= $data . PHP_EOL;
		}

		$writer = new FileWriter($finder);
		if (! $finder->isDir($buildDir)) {
			if (! $writer->mkdir($buildDir, 0755, true)) {
				$path = $finder->getPath($buildDir);
				$err = "could not create dir at -({$path})";
				throw new RunTimeException($err);
			}
		}

		$ok = $writer->putContent($result, $jsBuildFile);

	}

	protected function buildAllPages()
	{

	}

	/**
	 * @param	string	$page
	 * @return	null
	 */
	protected function buildPage($page)
	{

	}
}
