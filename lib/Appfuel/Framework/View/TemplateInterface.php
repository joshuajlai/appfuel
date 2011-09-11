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
 * Interface needed by the framework to use view templates
 */
interface TemplateInterface
{
	public function fileExists();
	public function setFile($file);
	public function createViewFile($filePath);
	public function getScope();
	public function setScope(ScopeInterface $scope);
	public function createScope(array $data = array());
	public function build(array $data = array(), $isPrivate = false);
	public function __toString();
}
