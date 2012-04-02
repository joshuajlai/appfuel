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
namespace Appfuel\Html;

use LogicException,
	RunTimeException,
	InvalidArgumentException,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileReader,
	Appfuel\Html\Resource\PkgName,
	Appfuel\Html\Resource\FileStack,
	Appfuel\Html\Resource\Yui3FileStack,
	Appfuel\Html\Resource\Yui3Manifest,
	Appfuel\Html\Resource\Yui\Yui3ResourceAdapter,
	Appfuel\Html\Resource\FileStackInterface,
	Appfuel\Html\Resource\AppfuelManifest,
	Appfuel\Html\Resource\AppViewManifest,
	Appfuel\Html\Resource\ResourceTree,
	Appfuel\Html\Tag\GenericTagInterface;

/**
 * Builds and configures an html page using an HtmlPageDetailInterface
 */
class HtmlPageConfiguration implements HtmlPageConfigurationInterface
{

	public function loadResourceTree()
	{
		$reader = new FileReader(new FileFinder('app'));
		$data   = $reader->decodeJsonAt('resource-tree.json');
		ResourceTree::setTree($data);
	}

	/**
	 * @return	bool
	 */
	public function isResourceTree()
	{
		return ResourceTree::isTree();
	}

	public function applyView($pkg, HtmlPageInterface $page)
	{
		if (! is_string($pkg) || empty($pkg)) {
			$err = "view package must be an non empty string";
			throw new InvalidArgumentException($err);
		}

		$pkgName  = new PkgName($pkg);
		$vendor   = $pkgName->getVendor();
		$viewName = $pkgName->getName();
		if (! $this->isResourceTree()) {
			$this->loadResourceTree();
		}

		$pkg = ResourceTree::getPackageByType($vendor, 'app-view', $viewName);
		$manifest = new AppViewManifest($pkg);
		$chrome = new PkgName($manifest->getHtmlPage(), $vendor);

		$config = ResourceTree::getPackageByType(
			$chrome->getVendor(), 
			'chrome',
			$chrome->getName()
		); 
		$adapter = new Yui3ResourceAdapter();
		$layer   = $adapter->buildLayer('fw-global');
		$files   = $layer->getAllJsSourcePaths();
		foreach ($files as $file) {
			$page->addScript($file);
		}	
	}

	/**
	 * @param	array	$config
	 * @param	HtmlPageInterface $page
	 * @return	null
	 */
	public function apply(array $config, HtmlPageInterface $page)
	{
		if (isset($config['html-head']) && is_array($config['html-head'])) {
			$this->applyHead($config['html-head'], $page);
		}

		if (isset($config['html-body']) && is_array($config['html-body'])) {
			$this->applyBody($config['html-body'], $page);
		}
	}

	public function applyHead(array $config, HtmlPageInterface $page)
	{
		if (isset($config['title'])) {
			$this->applyTitle($config['title'], $page);
		}
	
		if (isset($config['base'])) {
			$this->applyBase($config['base'], $page);
		}

		if (isset($config['meta'])) {
			$this->applyMeta($config['meta'], $page);
		}

		if (isset($config['css-links']) && is_array($config['css-links'])) {
			$this->applyCssFiles($config['css-links'], $page);
		}
	}
	
	/**
	 * @param	array	$config
	 * @param	HtmlPageInterface $page
	 * @return	null
	 */
	public function applyBody(array $config, HtmlPageInterface $page)
	{
		if (isset($config['js-scripts']) && is_array($config['js-scripts'])) {
			$this->applyJsFiles($config['js-scripts'], $page);
		}


	}

	/**
	 * @param	array	$config
	 * @param	HtmlPageInterface	$page
	 * @return	null
	 */
	public function applyTitle($config, HtmlPageInterface $page)
	{
        $sep    = null;
        $text   = '';
		$action = 'replace';
        if (is_string($config)) {
            $text = $config;
        }
        else if (is_array($config)) {
			if (isset($config['sep'])) {
				$sep = $config['sep'];
            }

            if (isset($config['text'])) {
                $text = $config['text'];
            }

			if (isset($config['action'])) {
				$action = $config['action'];
			}
        }

        $title = $page->getHtmlTag()
					  ->getHead()
					  ->getTitle();
		$title->addContent($text, $action);

		if (null !== $sep) {
			$title->setContentSeparator($sep);
		}
	}

	/**
	 * @param	mixed	$config
	 * @param	HtmlPageInterface $pafge
	 * @return	null
	 */
	public function applyBase($config, HtmlPageInterface $page)
	{
		$href = null;
        $target = null;

        if (is_string($config)) {
            $href = $config;
        }
        else if (is_array($config)) {
			if (isset($config['href'])) {
                $href = $config['href'];
            }

            if (isset($config['target'])) {
                $target = $config['target'];
            }
        }

		if (null !== $href || null !== $target) {
			$page->setHeadBase($href, $target);
		}
	}

	/**
	 * @param	array	$list
	 * @param	HtmlPageInterface $page
	 * @return	null
	 */
	public function applyMeta(array $list, HtmlPageInterface $page)
	{
		
		foreach ($list as $data) {
			$name    = isset($data['name']) ? $data['name'] : null;
			$content = isset($data['content']) ? $data['content']: null;
			$equiv   = isset($data['http-equiv']) ? $data['http-equiv'] : null;
			$charset = isset($data['charset']) ? $data['charset'] : null;
			$page->addHeadMeta($name, $content, $equiv, $charset);	
		}
		
	}

	/**
	 * @param	array	$config
	 * @param	HtmlPageInterface
	 * @return	null
	 */
	public function applyCssFiles(array $data, HtmlPageInterface $page)
	{
		$css  = null;
		$rel  = 'stylesheet';
		$type = 'text/css';
		foreach ($data as $idx => $file) {
			if (is_string($file)) {
				$css = $file;
			}
			else if (is_array($file)) {
				if ($file === array_values($file)) {
					$css  = current($file);
					$rel  = next($file);
					$type = next($file);
				}
				else {
					if (isset($file['href'])) {
						$css = $file['href'];
					}
			
					if (isset($file['rel'])) {
						$rel = $file['rel'];
					}

					if (isset($file['type'])) {
						$type = $file['type'];
					}
				}
			}
			else if ($file instanceof GenericTagInterface) {
				$page->addCssLinkTag($file);
				continue;
			}
			else {
				$err  = 'css file has been set but its format was not ';
				$err .= 'recognized, css file can be a string, indexed array,';
				$err .= 'associative array or an object that implements ';
				$err .= 'Appfuel\View\Html\Tag\GenericTagInterface ';
				$err .= "-($idx)";
				throw new InvalidArgumentException($err);
			}

			if (! is_string($css) || empty($css)) {
				$err  = "can not configure the html page: ";
				$err .= "css file at index -($idx) must be a non empty string";
				throw new InvalidArgumentException($err);
			}

			$page->addCssLink($css, $rel, $type);
		}
	}

	/**
	 * @param	mixed	$config
	 * @param	HtmlPageInterface $pafge
	 * @return	null
	 */
	public function applyInlineCss($config, HtmlPageInterface $page)
	{

	}

	/**
	 * @param	array	$config
	 * @param	HtmlPageInterface
	 * @return	null
	 */
	public function applyJsFiles(array $data, HtmlPageInterface $page)
	{
		foreach ($data as $index => $file) {
			if (! is_string($file) || empty($file)) {
				$err = "js file at -($index) must be a non empty string";
				throw new InvalidArgumentException($err);
			}

			$page->addScript($file, "text/javascript");
		}
	}

	/**
	 * @param	mixed	$config
	 * @param	HtmlPageInterface $pafge
	 * @return	null
	 */
	public function applyInlineJs($config, HtmlPageInterface $page)
	{

	}
}
