<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Test\Appfuel\App\MyRootAction;

use Appfuel\Framework\App\Action\ActionBuilderInterface,
	Appfuel\Framework\App\Route\RouteInterface,
	Appfuel\Framework\App\Action\InputSchemeInterface;

/**
 * The purpose of this class is to simulate a different implementation of the
 * the action builder. This allows testing of the front controllers ability
 * to check for the existence of an ActionBuilder class in different namespaces
 */
class ActionBuilder implements ActionBuilderInterface
{
	public function getRoute(){}
	public function isError(){}
	public function setError($text){ return $this;}
	public function createController(){}
    public function isInputValidationEnabled(){}
    public function enableInputValidation(){}
    public function disabledInputValidation(){}
    public function createInputScheme(){}
    public function createInputValidator(InputSchemeInterface $scheme){}
    public function createViewManager(){}
    public function buildView($responseType){}
}

