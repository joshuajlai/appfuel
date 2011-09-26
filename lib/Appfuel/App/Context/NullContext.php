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
namespace Appfuel\App\Context;

use Appfuel\Framework\Action\NullControllerNamespace;

/**
 * Used when running the framework that depends on the context but the context
 * is not required for your needs.
 */
class NullContext extends AppContext
{
	/**
	 * @param	ContextUriInterface		$uri
	 * @param	ContextInputInterface	$input
	 * @param	OperationalRouteInterface $opRoute
	 * @return	Context
	 */
	public function __construct()
	{
		$this->routeString	  = 'af-null-route';
		$this->uriParamString = '';
		$this->uriString	  = 'af-null-route';

		$this->ctrNamespace  = new NullControllerNamespace();
		$this->accessPolicy  = 'public';
		$this->defaultFormat = 'text';
		$this->requestType   = 'text';
		$this->preFilters    = array();
		$this->postFilters   = array();

		$this->input = new ContextInput('get');
	}
}
