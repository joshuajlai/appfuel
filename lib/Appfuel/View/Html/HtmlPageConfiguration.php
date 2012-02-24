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

use RunTimeException,
	InvalidArgumentException;

/**
 * Builds and configures an html page using an HtmlPageDetailInterface
 */
class HtmlPageConfiguration implements HtmlPageConfigurationInterface
{

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
	}

	public function applyHtmlDoc($doc, HtmlPageInterface $page)
	{

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
	public function applyCssFiles(array $config, HtmlPageInterface $page)
	{

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
	public function applyJsFiles(array $config, HtmlPageInterface $page)
	{

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
