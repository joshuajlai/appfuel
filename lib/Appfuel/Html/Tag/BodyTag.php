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
namespace Appfuel\Html\Tag;

use LogicException;

/**
 * The html body tag. Delegates attributes to TagAttributeInterface and 
 * delegates content to TagContentInterface. The tag name on this class 
 * can not be changed (can be upper or lower case).
 */
class BodyTag extends GenericTag
{
	/**
	 * Only has two valid attributes href and target
	 *
	 * @return	base
	 */
	public function __construct($data = null, $sep = null)
	{
		if (null === $sep) {
			$sep = PHP_EOL;
		}

		$content = new TagContent($data, $sep);
		$attrs   = new TagAttributes(array(
			'onafterprint',
			'onbeforeprint',
			'onbeforeunload',
			'onblur',
			'onerror',
			'onfocus',
			'onhashchange',
			'onload',
			'onmessage',
			'onoffline',
			'ononline',
			'onpagehide',
			'onpageshow',
			'onpopstate',
			'onresize',
			'onscroll',
			'onstorage',
			'onunload'
		));
		
		parent::__construct('body', $content, $attrs);
	}

    /**
     * Fix the tag to only be a body tag
     *
     * @param   string  $name
     * @return  HeadTag
     */
    public function setTagName($name)
    {  
        if (! is_string($name) || 'body' !== strtolower($name)) {
            $err = 'this tag can only be a head tag';
            throw new LogicException($err);
        }

        return parent::setTagName($name);
    }
}
