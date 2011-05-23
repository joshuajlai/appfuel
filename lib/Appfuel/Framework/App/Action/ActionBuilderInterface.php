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
namespace Appfuel\Framework\App\Action;

use Appfuel\Framework\App\Route\RouteInterface;

/**
 *
 */
interface ActionBuilderInterface
{
	public function getRoute();
	public function createController();
	public function isError();
	public function setError($text);
/*	public function isInputValidationEnabled();
	public function enableInputValidation();
	public function disabledInputValidation();
	public function createInputScheme();
	public function createInputValidator(InputSchemeInterface $scheme);
	public function createViewManager();
	public function buildView($responseType);
*/
}
