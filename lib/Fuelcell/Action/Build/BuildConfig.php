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
namespace Fuelcell\Action\Build;

use DomainException,
	Appfuel\Kernel\ConfigBuilder,
	Appfuel\Kernel\Mvc\MvcAction,
	Appfuel\Kernel\Mvc\MvcContextInterface;

/**
 * This will take the config file specified by env merge it with config 
 * data from the production env and then write the resulting file to 
 * app/config/config.php. Each env config file is expected to return an
 * associative array of config settings.
 */
class BuildConfig extends MvcAction
{
	/**
	 * @param	MvcContextInterface $context
	 * @return	null
	 */
	public function process(MvcContextInterface $context)
	{
		$input = $context->getInput();
		$env   = $input->get('get', 'env');
		if (! is_string($env) || empty($env)) {
			$err = 'env must be a non empty string like -(prod, qa, etc..)';
			throw new DomainException($err);
		}

		$builder = new ConfigBuilder($env);
		$builder->generateConfigFile();
		$view = $context->getView();
		$view->assign('result', "config was built for -($env)");
	}
}
