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
namespace Appfuel\Framework\App\View;

/**
 * Interface needed by the framework to use view templates
 */
interface CompositeTemplateInterface extends TemplateInterface
{
	public function templateExists($key);
	public function getTemplate($key);
	public function addTemplate($key, TemplateInterface $template);
}
