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

/**
 * Builds and configures an html page using an HtmlPageDetailInterface
 */
interface HtmlPageConfigurationInterface
{
	/**
	 * @param	array	$data
	 * @param	HtmlPageInterface	$page
	 * @return	null
	 */
	public function apply(array $data, HtmlPageInterface $page);

	/**
	 * @param	mixed  string|ViewTemplateInterface
	 * @param	HtmlPageInterface $page
	 * @retunr	null
	 */
	public function applyHtmlDoc($data, HtmlPageInterface $page);

	/**
	 * @param	array	$data
	 * @param	HtmlPageInterface	$page
	 * @return	null
	 */
	public function applyTitle($data, HtmlPageInterface $page);

	/**
	 * @param	mixed	$data
	 * @param	HtmlPageInterface $pafge
	 * @return	null
	 */
	public function applyBase($data, HtmlPageInterface $page);

	/**
	 * @param	mixed	$data
	 * @param	HtmlPageInterface $page
	 * @return	null
	 */
	public function applyCssFiles(array $data, HtmlPageInterface $page);

	/**
	 * @param	mixed	$data
	 * @param	HtmlPageInterface $page
	 * @return	null
	 */
	public function applyInlineCss($data, HtmlPageInterface $page);

	/**
	 * @param	mixed	$data
	 * @param	HtmlPageInterface $page
	 * @return	null
	 */
	public function applyJsFiles(array $data, HtmlPageInterface $page);

	/**
	 * @param	mixed	$data
	 * @param	HtmlPageInterface $page
	 * @return	null
	 */
	public function applyInlineJs($data, HtmlPageInterface $page);
}
