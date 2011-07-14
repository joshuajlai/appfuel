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
namespace Appfuel\Framework\View;

/**
 * Allows for a composite template pattern to be used for tempates.
 * The composite template can hold leaf templates (templates) and other
 * composite templates.
 */
interface CompositeTemplateInterface extends TemplateInterface
{
	public function templateExists($key);
	public function getTemplate($key);
	public function addTemplate($key, TemplateInterface $template);
}
