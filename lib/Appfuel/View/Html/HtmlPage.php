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

use Appfuel\View\Compositor\TextCompositor,
	Appfuel\View\ViewTemplate,
	Appfuel\View\ViewTemplateInterface;

/**
 * Template used to generate generic html documents
 */
class HtmlPage extends ViewTemplate
{
	/**
	 * @param	FileViewInterface $content	main html body content
	 * @param	FileViewInterface $inlineJs inline js content 
	 * @param	HtmlDocInterface  $doc		html doc template
	 * @return	HtmlPage
	 */
	public function __construct(FileViewInterface $content,
								FileViewInterface $inlineJs = null,
								HtmlDocInterface  $doc = null)
	{
		if (null == $doc) {
			$doc = new HtmlDocTemplate();
		}

		$this->addTemplate('htmldoc', $doc)
			 ->addTemplate('content', $content);
		
		if (null !== $inlneJs) {
			$this->addTemplate('inline-js', $inlineJs);
		}

		$data = null;
		$viewCompositor = new TextCompositor(null, null, 'values');
		parent::__construct($data, $viewCompositor);
	}

	public function build()
	{
		$content = $this->getTemplate('content');
		$htmlDoc = $this->getTemplate('htmldoc');
		if (null === $htmlDoc) {
			return 'error: no html doc template found';
		}
	
		$data = '';
		if (null !== $content) {
			$data = $content->build();
		}
		$htmlDoc->addBodyContent($data);
		
		$inlineJs = $this->getTemplate('inline-js');
		if (null !== $inlineJs) {
			$htmlDoc->addJsBodyInlineContent($inlineJs->build());
		}

		return $htmldoc->build();
	}
}
