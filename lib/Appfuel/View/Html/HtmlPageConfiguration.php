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

	}

	public function applyHtmlDoc($doc, HtmlPageInterface $page)
	{

	}

	/**
	 * @param	array	$config
	 * @param	HtmlPageInterface	$page
	 * @return	null
	 */
	public function applyTitle($config, HtmlPageInterface $page)
	{
        $sep   = null;
        $text  = '';
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
        }
            
		$page->setHeadTitle($title, $sep);
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
			if (isset($base['href'])) {
                $href = $config['href'];
            }

            if (isset($config['target'])) {
                $target = $config['target'];
            }
        }
        
		$page->setBase($href, $target);
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
