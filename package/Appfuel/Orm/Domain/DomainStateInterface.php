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
namespace Appfuel\Orm\Domain;

/**
 * Keeps track of the state of the domain. During the lifecycle of a domain
 * it will go through the following states:
 *
 *	marshal	  :	the domain is marshaled from the datasource
 *	new		  : the domain needs to be added into the datasource
 *	dirty	  : the domain has changed and needs to be updated
 *	delete	  : the domain needs to be removed from the datasource
 */
interface DomainStateInterface
{
	/**
	 * Every domain must have an id for which it identified
	 * 
	 * @return	mixed 
	 */
	public function getState();

	/**
	 * @param	mixed	$id		
	 * @return	DomainModelInterface
	 */
	public function setState($code);

	/**
	 * @return bool
	 */
	public function isMarshal();
	
	/**
	 * @return bool
	 */
	public function isNew();
	
	/**
	 * @return bool
	 */
	public function isDirty();

	/**
	 * @return bool
	 */
	public function isDelete();

	/**
	 * Mark the state as marshal and set the intial members
	 * 
	 * @param	array	$members
	 * @return	DomainState
	 */
	public function markMarshal(array $members = null);

	/**
	 * @param	string	$member		domain member that has changed
	 * @return	DomainState
	 */
	public function markDirty($member);

	/**
	 * Return a list of members that the domain was marshaled with
	 *
	 * @return	array
	 */
	public function getInitialMembers();

	/**
	 * Get a list of all dirty members and their values
	 *
	 * @return	array
	 */ 
	public function getDirtyMembers();


	/**
	 * Determines if a domain member is dirty	
	 * 
	 * @return bool
	 */
	public function isDirtyMember($member);

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
}
