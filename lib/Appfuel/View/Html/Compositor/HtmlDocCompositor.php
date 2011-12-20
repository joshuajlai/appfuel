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
namespace Appfuel\View\Html\Compositor;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\Kernel\PathFinder,
	Appfuel\Kernel\PathFinderInterface,
	Appfuel\View\Compositor\FileCompositor;

/**
 * This class exposes the functionality in the .phtml file pointed to by
 * $file. All public methods are exposed through $this. This class extends
 * the regular html compositor by adding functionality for doc type, 
 * javascript and css.
 */
class HtmlDocCompositor 
	extends FileCompositor implements HtmlDocCompositorInterface
{
    /**
	 * File and pathfinder are optional. If you need to use a different 
	 * template in the same template path then use file to specify the path
	 * to the template. If that template is located outside of the template
	 * dir then use a path finder object to set the relative root path of 
	 * that template and use file to hold the relative path of the template 
	 * itself.
	 *
     * @param   array   $data
     * @return  Template
     */
    public function __construct($file = null, 
								PathFinderInterface $pathFinder = null)
    {
		if (null === $file) {
			$file = 'appfuel/html/htmldoc.phtml';
		}
		$this->setFile($file);
		parent::__construct($pathFinder);

    }

	/**
	 * @return	string
	 */
	public function getDefaultCharset()
	{
		return '<meta http-equiv="Content-Type" ' .
				'content="text/html; charset=utf-8">';
	}

	/**
	 * Always true unless a strict (bool) false is assigned to 'is-css-enabled'
	 *
	 * @return	bool
	 */
	public function isCssEnabled()
	{
		$isCss = $this->get('is-css-enabled', true);
		return (false === $isCss) ? false : true;
	}

	/**
	 * @return	bool
	 */
	public function isInlineCssEnabled()
	{
		$isCss = $this->get('is-inlinecss-enabled', true);
		return (false === $isCss) ? false : true;
	}

	/**
	 * @return	bool
	 */
	public function isJsEnabled()
	{
		$isjs = $this->get('is-js-enabled', true);
		return (false === $isjs) ? false : true;
	}

	/**
	 * @return	bool
	 */
	public function isJsHeadInlineEnabled()
	{
		$isjs = $this->get('is-jsinline-head-enabled', true);
		return (false === $isjs) ? false : true;
	}

	/**
	 * @return	bool
	 */
	public function isJsBodyInlineEnabled()
	{
		$isjs = $this->get('is-jsinline-body-enabled', true);
		return (false === $isjs) ? false : true;
	}

	/**
	 * @param	string	$type
	 * @return	null
	 */
	public function renderDocType($type)
	{
		echo $this->getDocType($type);
	}

	/**
	 * @param	string	$type
	 * @return	string
	 */
	public function getDocType($type)
	{
		switch ($type) {
			case 'html5': 
				$text = '<!DOCTYPE HTML>';
				break;

			case 'html401-strict':
				$text = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">';
				break;
			case 'html401-transitional':
				$text = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">';
				break;
			case 'html401-frameset':
				$text = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
   "http://www.w3.org/TR/html4/frameset.dtd">';
				break;
			case 'xhtml10-strict':
				$text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
				break;
			case 'xhtml10-transitional':
				$text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
			case 'xhtml10-frameset':
				$text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">';
				break;
			case 'xhtml11':
				$text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
				break;
			case 'xhtml11-basic':
				$text = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN"
    "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">';
				break;
			case 'mathml20':
				$text = '<!DOCTYPE math PUBLIC "-//W3C//DTD MathML 2.0//EN"	
	"http://www.w3.org/Math/DTD/mathml2/mathml2.dtd">';
				break;
			case 'mathml101':
				$text = '<!DOCTYPE math SYSTEM 
	"http://www.w3.org/Math/DTD/mathml1/mathml.dtd">';
				break;
			case 'xhtml+mathml+svg':
				$text = '<!DOCTYPE html PUBLIC
    "-//W3C//DTD XHTML 1.1 plus MathML 2.0 plus SVG 1.1//EN"
    "http://www.w3.org/2002/04/xhtml-math-svg/xhtml-math-svg.dtd">';
				break;
			case 'xhtml-host+mathml+svg':
				$text = '<!DOCTYPE html PUBLIC
    "-//W3C//DTD XHTML 1.1 plus MathML 2.0 plus SVG 1.1//EN"
    "http://www.w3.org/2002/04/xhtml-math-svg/xhtml-math-svg.dtd">';
				break;
			case 'xhtml+mathml+svg-host':
				$text = '<!DOCTYPE svg:svg PUBLIC
    "-//W3C//DTD XHTML 1.1 plus MathML 2.0 plus SVG 1.1//EN"
    "http://www.w3.org/2002/04/xhtml-math-svg/xhtml-math-svg.dtd">';
				break;
			case 'svg11-full':
				$text = '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN"
	"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">';
				break;
			case 'svg10':
				$text = '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN"
	"http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">';
				break;
			case 'svg11-basic':
				$text = '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1 Basic//EN"
	"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11-basic.dtd">';
				break;
			case 'svg11-tiny':
				$text = '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1 Tiny//EN"
	"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11-tiny.dtd">';
				break;
			default: $text = '<!DOCTYPE HTML>';
			
		}
			
		return $text;
	}
}
