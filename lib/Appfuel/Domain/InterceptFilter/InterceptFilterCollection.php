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
namespace Appfuel\Domain\InterceptFilter;

use Appfuel\Framework\Exception,
	Appfuel\Orm\Domain\DomainCollection,
	Appfuel\Framework\Orm\Domain\DomainModelInterface,
	Appfuel\Framework\Domain\InterceptFilter\InterceptFilterDomainInterface;

/**
 * Common functionality for every orm domain model
 */
class InterceptFilterCollection extends DomainCollection
{
	/**
	 * The type of domain is immutable and can not be change during the 
	 * lifespan of the collection.
	 *
	 * @param	string	$type		class or interface of domain
	 * @return	DomainCollection
	 */
	public function __construct()
	{
		parent::__construct('af-intercept');
	}

	/**
	 * @return	bool
	 */
	public function valid()
	{
		return $this->current() instanceof InterceptFilterDomainInterface;
	}

	/**
	 * @param	DomainModelInterface	$domain
	 * @return	null
	 */
	public function add(DomainModelInterface $domain)
	{
		if (! ($domain instanceof InterceptFilterDomainInterface)) {
			throw new Exception("must be an Intercept Filter Domain");
		}

		return parent::add($domain);
	}
}
