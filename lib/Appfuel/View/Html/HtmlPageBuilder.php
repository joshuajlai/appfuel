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
namespace Appfuel\View\Html;

use InvalidArgumentException,
	Appfuel\Kernel\PathFinder,
	Appfuel\View\Html\Resource\HtmlResourceManager,
	Appfuel\View\Html\Tag\HtmlTagFactory,
	Appfuel\View\Html\Tag\HtmlTagFactoryInterface;


/**
 * Template used to generate generic html documents
 */
class HtmlPageBuilder implements HtmlPageBuilderInterface
{
	/**
	 * @var	HtmlTagFactoryInterface
	 */
	protected $tagFactory = null;

	/**
	 * @var	PathFinder
	 */
	protected $pathFinder = null;

	/**
	 * Defaults to the appfuel template file 
	 *
	 * @param	string				$file		relative path to template file
	 * @param	PathFinderInterface $pathFinder	resolves the relative path
	 * @return	HtmlDocTemplate
	 */
	public function __construct(HtmlTagFactoryInterface $tagFactory = null,
								PathFinder $pathFinder = null)
	{
		if (null === $tagFactory) {
			$tagFactory = new HtmlTagFactory();
		}
		$this->tagFactory = $tagFactory;
		
		if (null === $pathFinder) {
			$pathFinder = new PathFinder(ResourceManager::getResourceDir());
		}
		$this->pathFinder = $pathFinder;
	}

	/**
	 * @return	TagFactoryInterface
	 */
	public function getTagFactory()
	{
		return $this->tagFactory;
	}

	/**
	 * @return	PathFinderInterface
	 */
	public function getPathFinder()
	{
		return $this->pathFinder;
	}

	/**
	 * @return	string
	 */
	public function configurePage(HtmlPageInterface $page, array $data)
	{
		if (isset($data['html-head']) && is_array($data['html-head'])) {
			$this->configurePageHead($page, $data['html-head']);
		}

		if (isset($data['html-body']) && is_array($data['html-body'])) {
			$this->configurePageHead($page, $data['html-body']);
		}
	}

	/**
	 * @param	HtmlPageInterface $page	
	 * @param	array	$data
	 * @return	null
	 */
	public function configurePageHead(HtmlPageInterface $page, array $data)
	{
		/* check for title */
		if (isset($data['title'])) {
			$title = $data['title'];
			$sep   = null;
			$text  = '';
			if (is_string($title)) {
				$text = $title;
			}
			else if (is_array($title)) {
				if (isset($title['sep'])) {
					$sep = $title['sep'];
				}

				if (isset($title['text'])) {
					$text = $title['text'];
				}
			}
			$page->setHeadTitle($title, $sep);
		}

		/* check for base tag */
		if (isset($data['base'])) {
			$base = $data['base'];
			$href = null;
			$target = null;

			if (is_string($base)) {
				$href = $base;
			}
			else if (is_array($base)) {
				if (isset($base['href'])) {
					$href = $base['href'];
				}

				if (isset($base['target'])) {
					$target = $base['target'];
				}
				$page->setBase($href, $target);
			}
		}

		/* check for meta tags */
		if (isset($data['meta']) && is_array($data['meta'])) {
			$metalist = $data['meta'];
			foreach ($metalist as $meta) {
				if ($meta instanceof GenericTagInterface) {
					$page->addHeadMetaTag($meta);
				}
				else if (is_array($meta)) {
					$name	 = null;
					$content = null;
					$equiv	 = null;
					$charset = null;
					if (isset($meta['name'])) {
						$name = $meta['name'];
					}

					if (isset($meta['content'])) {
						$content = $meta['content'];
					}

					if (isset($meta['http-equiv'])) {
						$equiv = $meta['http-equiv'];
					}
					
					if (isset($meta['charset'])) {
						$charset = $meta['charset'];
					}

					$page->addHeadMeta($name, $content, $equiv, $charset);
				}
			}
		}

		/* check for css files */
		if (isset($data['css-files']) && is_array($data['css-files'])) {
			$csslist = $data['css-files'];
			foreach ($csslist as $css) {
				if ($css instanceof GenericTagInterface) {
					$page->addCssTag($css);
					continue;
				}

				$src  = null;
				$rel  = null;
				$type = null; 
				if (is_string($css) && ! empty($src)) {
					$src = $css;
				}
				else if (is_array($css)) {
					if (isset($css['src'])) {
						$src = $css['src'];
					}

					if (isset($css['rel'])) {
						$rel = $css['rel'];
					}

					if (isset($css['type'])) {
						$type = $css['type'];
					}
				}
				$page->addCssLink($src, $rel, $type);
			}
		}
	}

	/**
	 * @param	HtmlPageInterface $page	
	 * @param	array	$data
	 * @return	null
	 */
	public function configurePageBody(HtmlPageInterface $page, array $data)
	{

	}

	/**
	 * @throws	InvalidArgumentException
	 * @throws	RunTimeException
	 * @param	string	$filePath
	 * @return	array
	 */
	public function getConfiguration($filePath)
	{
		if (! is_string($filePath) || empty($filePath)) {
			$err = 'file path to configuration must be a non empty array';
			throw new InvalidArgumentException($err);
		}

		$finder = $this->getPathFinder();
		$fullPath = $finder->getPath($filePath);
		if (! file_exists($fullPath)) {
			$err = "page config file could not be found at -($fullPath)";
			throw new RunTimeException($err);
		}

		return require $fullPath;
	}
	
	/**
	 * @throws	RunTimeException
	 * @param	HtmlViewInterface	$view
	 * @return	HtmlPageInterface
	 */
	public function buildPage(HtmlViewInterface $view)
	{
		$htmlDocTpl = $view->getHtmlDocTpl();
		if (empty($htmlDocTpl) || ! is_string($htmlDocTpl)) {
			$htmlDocTpl = null;
		}

		$tagFactory = null;
		$tagFactoryClass = $view->getTagFactoryClass();
		if (is_string($tagFactoryClass) && ! empty($tagFactoryClass)) {
			$tagFactory = new $tagFactoryClass();
		}

		$filePath = $view->getPathConfigurationFile();
		if (! is_string($filePath) || empty($filePath)) {
			$filePath = 'appfuel/html/doc/default.php';
		}
		$config = $this->getConfiguration($filePath);

		$htmlPageClass = $view->getHtmlPageClass();
		if (is_string($htmlPageClass) && !empty($htmlPageClass)) {
			$page = new $htmlPageClass($view, $htmlDocTpl, $tagFactory);
			if (! ($page instanceof HtmlPageInterface)) {
				$err  = 'custom html page class must implment Appfuel\View';
				$err .= '\Html\HtmlPageInterface';
				throw new RunTimeException($err);
			}
		}
		else {
			$page = $this->createPage($view, $htmlDocTpl, $tagFactory);
		}

		$this->configurePage($page, $config);
		return $page;
	}

	/**
	 * @param	mixed	string|object|ViewInterface $view
	 * @param	string	$docfile
	 * @param	HtmlTagFactoryInterface	$factory
	 * @return	HtmlPage
	 */
	public function createHmtlPage($view, 
								   $docFile = null,
									HtmlTagFactoryInterface $factory = null)
	{
		return new HtmlPage($view, $docFile, $factory);
	}
}
