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

use Appfuel\View\ViewTemplate,
	Appfuel\View\ViewInterface;

/**
 * Template used to generate generic html documents
 */
class HtmlPageTemplate extends ViewTemplate implements HtmlPageInterface
{
	/**
	 * @param	FileViewInterface $content	main html body content
	 * @param	FileViewInterface $inlineJs inline js content 
	 * @param	HtmlDocInterface  $doc		html doc template
	 * @return	HtmlPage
	 */
	public function __construct(ViewInterface $content,
								ViewInterface $inlineJs = null,
								HtmlDocInterface  $doc = null)
	{
		if (null == $doc) {
			$doc = new HtmlDocTemplate();
		}

		$this->setHtmlDoc($doc)
			 ->setHtmlContent($content);

		if (null !== $inlineJs) {
			$this->setInlineJs($inlineJs);
		}
	}

	/**
	 * @param	HtmlDocInterface $doc
	 * @return	HtmlPage
	 */
	public function setHtmlDoc(HtmlDocInterface $doc)
	{
		return $this->addTemplate('htmldoc', $doc);
	}

	/**	
	 * @return	HtmlDocInterface | null when not set
	 */
	public function getHtmlDoc()
	{
		return $this->getTemplate('htmldoc');
	}

	/**
	 * @param	FileViewInterface $view
	 * @return	HtmlPage
	 */
	public function setHtmlContent(ViewInterface $view)
	{
		return $this->addTemplate('content', $view);
	}

	/**	
	 * @return	FileViewInterface	| null when not set
	 */
	public function getHtmlContent()
	{
		return $this->getTemplate('content');
	}

	/**
	 * @param	FileViewInterface $view
	 * @return	HtmlPage
	 */
	public function setInlineJs(ViewInterface $js)
	{
		return $this->addTemplate('html-inline-js', $js);
	}

	/**	
	 * @return	FileViewInterface	| null when not set
	 */
	public function getInlineJs()
	{
		return $this->getTemplate('html-inline-js');
	}

	/**
	 * @return	string
	 */
	public function build()
	{
		$htmlDoc = $this->getHtmlDoc();
		if (null === $htmlDoc) {
			return 'error: no html doc template found';
		}
	
		$data = '';
		$content = $this->getHtmlContent();
		if (null !== $content) {
			$data = $content->build();
		}
		$htmlDoc->addBodyContent($data);
		
		$inlineJs = $this->getInlineJs();
		if (null !== $inlineJs) {
			$htmlDoc->addJsBodyInlineContent($inlineJs->build());
		}

		return $htmlDoc->build();
	}
}
