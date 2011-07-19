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
namespace Appfuel\Framework\Orm\Domain;

/**
 * Functionality needed to function as a domain
 */
interface DomainModelInterface
{
	/**
	 * Used to indicate that a member attribute has changed 
	 * 
	 * @param	string	$member		name of the domain attr thats changed
	 * @return	bool
	 */
	public function markDirty($member);

	/**
	 * Used to remove a single member or all members from being marked dirty.
	 * When $member is null all members should be marked clean
	 *
	 * @param	string	$member	 domain attr to mark clean. 
	 * @return	bool
	 */
	public function markClean($member = null);

	/**
	 * Used to indicate that the domain state is new
	 * @return	null
	 */
	public function markNew();

	/**
	 * Used to indicate that the domain is in a state of deletion
	 * 
	 * @return	null
	 */
	public function markDelete();

	/**
	 * Determines that state of the damain
	 * 
	 * @return	DomainState
	 */
	public function getDomainState();
	
	/**
	 * @param	DomainState $state
	 * @return	DomainModel
	 */
	public function setDomainState(DomainStateInterface $state);
}
