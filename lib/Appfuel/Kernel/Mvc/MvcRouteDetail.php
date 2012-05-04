<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * @license     http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Kernel\Mvc;

use DomainException,
    Appfuel\DataStructure\Dictionary;

/**
 * Facade made of RouteAccess for acl, RouteStartup for app tasks,
 * RouteIntercept for intercepting filters, RouteView for view data
 * and routeAction for Action info
 */
class MvcRouteDetail extends Dictionary implements MvcRouteDetailInterface
{
	/**
	 * Used by the app handler to add, remove or skip startup tasks
	 * @var array
	 */
	protected $startup = null;

	/**
	 * Used by the front controller to handle intercept filtering
	 * @var array
	 */
	protected $intercept = null;

	/**
	 * @var	 RouteAccess
	 */
	protected $access = null;

	/**
	 * @var RouteView
	 */
	protected $view = null;

	/**
	 * @var RouteAction
	 */
	protected $action = null;

	/**
	 * @param	array	$data
	 * @return	MvcRouteDetail
	 */
	public function __construct(array $data)
	{
		$this->initializeStartup($data);
		$this->initializeIntercept($data);
		$this->initializeAcl($data);
		$this->initializeView($data);
		$this->initializeAction($data);

		$params = array();
		if (isset($data['params']) && is_array($data['params'])) {
			$params = $data['params'];
		}
		parent::__construct($params);
	}

	/**
	 * @return	bool
	 */
	public function isIgnoreConfigStartupTasks()
	{
		return $this->getRouteStartup()
					->isIgnoreConfigStartupTasks();
	}

	/**
	 * @return	bool
	 */
	public function isPrependStartupTasks()
	{
		return $this->getRouteStartup()
					->isPrependStartupTasks();
	}

	/**
	 * @return	bool
	 */
	public function isStartupDisabled()
	{
		return $this->getRouteStartup()
					->isStartupDisabled();
	}

	/**
	 * @return	bool
	 */
	public function isStartupTasks()
	{
		return $this->getRouteStartup()
					->isStartupTasks();
	}

	/**
	 * @return	array
	 */
	public function getStartupTasks()
	{
		return $this->getRouteStartup()
					->getStartupTasks();
	}

	/**
	 * @return	bool
	 */
	public function isExcludedStartupTasks()
	{
		return $this->getRouteStartup()
					->isExcludedStartupTasks();
	}

	/**
	 * @return	array
	 */
	public function getExcludedStartupTasks()
	{
		return $this->getRouteStartup()
					->getExcludedStartupTasks();
	}


	/**
	 * @return string
	 */
	public function getFormat()
	{
		return $this->getRouteView()
					->getFormat();
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	public function setFormat($name)
	{
		$this->getRouteView()
			 ->setFormat($name);
		
		return $this;
	}

	/**
	 * @return	bool
	 */
	public function isPublicAccess()
	{
		return $this->getRouteAccess()
					->isPublicAccess();
	}

	/**
	 * @return	bool
	 */
	public function isInternalOnlyAccess()
	{
		return $this->getRouteAccess()
					->isInternalOnlyAccess();
	}

	/**
	 * @return bool
	 */
	public function isAclAccessIgnored()
	{
		return $this->getRouteAccess()
					->isAclAccessIgnored();
	}

	/**
	 * @param	string	$code
	 * @param	string	$method
	 * @return	bool
	 */
	public function isAccessAllowed($codes, $method = null)
	{
		return $this->getRouteAccess()
					->isAccessAllowed($codes, $method);
	}

	/**
	 * @return	bool
	 */
	public function isPreFilteringEnabled()
	{
		return $this->getRouteIntercept()
					->isPreFilteringEnabled();
	}

	/**
	 * @return	bool
	 */
	public function isPreFilters()
	{
		return $this->getRouteIntercept()
					->isPreFilters();
	}

	/**
	 * @return	array
	 */
	public function getPreFilters()
	{
		return $this->getRouteIntercept()
					->getPreFilters();
	}

	/**
	 * @return	bool
	 */
	public function isExcludedPreFilters()
	{
		return $this->getRouteIntercept()
					->isExcludedPreFilters(); 
	}

	/**
	 * @return	array
	 */
	public function getExcludedPreFilters()
	{
		return $this->getRouteIntercept()
					->getExcludedPreFilters();
	}

	/**
	 * @return	bool
	 */
	public function isPostFilteringEnabled()
	{
		return $this->getRouteIntercept()
					->isPostFilteringEnabled();
	}

	/**
	 * @return	bool
	 */
	public function isPostFilters()
	{
		return $this->getRouteIntercept()
					->isPostFilters(); 
	}

	/**
	 * @return array
	 */
	public function getPostFilters()
	{
		return $this->getRouteIntercept()
					->getPostFilters();
	}

	/**
	 * @return	bool
	 */
	public function isExcludedPostFilters()
	{
		return $this->getRouteIntercept()
					->isExcludedPostFilters(); 
	}

	/**
	 * @return array
	 */
	public function getExcludedPostFilters()
	{
		return $this->getRouteIntercept()
					->getExcludedPostFilters();
	}

	/**
	 * @return	bool
	 */
	public function isViewDisabled()
	{
		return $this->getRouteView()
					->isViewDisabled();
	}

	/**
	 * @return	bool
	 */
	public function isManualView()
	{
		return $this->getRouteView()
					->isManualView();
	}

	/**
	 * @return	bool
	 */
	public function isViewPackage()
	{
		return $this->getRouteView()
					->isViewPackage();
	}

	/**
	 * @return	string
	 */
	public function getViewPackage()
	{
		return $this->getRouteView()
					->getViewPackage();
	}

	/**
	 * @return	string
	 */
	public function getActionName()
	{
		return $this->getRouteAction()
					->getName();
	}

	/**
	 * @return	string | false
	 */
	public function findActionName($method = null)
	{
		return $this->getRouteAction()
					->findAction($method);
	}

	/**
	 * @param	array	$data
	 * @return	null
	 */
	protected function initializeStartup(array $data)
	{
		$startup = $this->createRouteStartup();
		$this->startup = $startup;
		if (! isset($data['startup'])) {
			return;
		}
		$data = $data['startup'];

		if (isset($data['is-disabled']) && true === $data['is-disabled']) {
			$startup->disableStartup();
		}

		if (isset($data['is-prepended']) && true === $data['is-prepended']) {
			$startup->prependStartupTasks();
		}
		
		if (isset($data['is-config-ignored']) && 
			true === $data['is-config-ignored']) {
			$startup->ignoreConfigStartupTasks();
		}

		if (isset($data['tasks'])) {
			$startup->setStartupTasks($data['tasks']);
		}

		if (isset($data['excluded-tasks'])) {
			$startup->setExcludedStartupTasks($data['excluded-tasks']);
		}

	}

	/**
	 * @param	array	$data
	 * @return	null
	 */
	protected function initializeIntercept(array $data)
	{
		/* store first so defaults are used when no data is found */
		$intercept = $this->createRouteIntercept();
		$this->intercept = $intercept;
		if (! isset($data['intercept'])) {
			return;
		}

		$data = $data['intercept'];
		if (isset($data['is-skip-pre']) && true === $data['is-skip-pre']) {
			$intercept->disablePreFiltering();
		}

		if (isset($data['include-pre'])) {
			$list = $data['include-pre'];
			if (is_string($list)) {
				$list = array($list);
			}
			$intercept->setPreFilters($list);
		}

		if (isset($data['exclude-pre'])) {
			$list = $data['exclude-pre'];
			if (is_string($list)) {
				$list = array($list);
			}
			$intercept->setExcludedPreFilters($list);
		}

		if (isset($data['is-skip-post']) && true === $data['is-skip-post']) {
			$intercept->disablePostFiltering();
		}

		if (isset($data['include-post'])) {
			$list = $data['include-post'];
			if (is_string($list)) {
				$list = array($list);
			}
			$intercept->setPostFilters($list);
		}

		if (isset($data['exclude-post'])) {
			$list = $data['exclude-post'];
			if (is_string($list)) {
				$list = array($list);
			}
			$intercept->setExcludedPostFilters($list);
		}
	}


	/**
	 * @param	array	$data
	 * @return	null
	 */
	protected function initializeAcl(array $data)
	{
		$acl = $this->createRouteAccess();
		$this->access = $acl;
		if (isset($data['access'])) {
			$data = $data['access'];	
		}

		if (isset($data['is-public']) && true === $data['is-public']) {
			$acl->enablePublicAccess();
		}

		if (isset($data['is-internal']) && true === $data['is-internal']) {
			$acl->enableInternalOnlyAccess();
		}

		if (isset($data['is-ignore']) && true === $data['is-ignore']) { 
			$acl->ignoreAclAccess();
		}

		$map = array();
		if (isset($data['acl-access'])) {
			$map = $data['acl-access'];
			$acl->setAclMap($map);	
		}
	}

	/**
	 * @param	array	$data
	 * @return	null
	 */
	protected function initializeView(array $data)
	{
		$view = $this->createRouteView();
		if (isset($data['is-view']) && false === $data['is-view']) {
			$view->disableView();
		}
		else if (isset($data['is-manual-view']) && 
				true === $data['is-manual-view']) {
			$view->enableManualView();
		}

		if (isset($data['view-pkg'])) {
			$view->setViewPackage($data['view-pkg']);
		}

		if (isset($data['default-format'])) {
			$view->setFormat($data['default-format']);
		}
		$this->view = $view;
	}

	/**
	 * @param	array	$data
	 * @return	null
	 */
	protected function initializeAction(array $data)
	{
		$action = $this->createRouteAction();
			
		if (! isset($data['action-name'])) {
			$err  = 'the action name must be set in order for the dispatcher ';
			$err .= 'to be able to create it';
			throw new DomainException($err);
		}
		$name = $data['action-name'];
		if (is_string($name)) {
			$action->setName($name);
		}
		else if (is_array($name)) {
			$action->setMap($name);
		}
		else {
			$err  = 'key -(action-name) must be non empty string or an ';
			$err .= 'array of method=>actionName mappings';
			throw new DomainException($err);
		}
		

		$this->action = $action;
	}

	/**
	 * @return	RouteAction
	 */
	protected function getRouteAction()
	{
		return $this->action;
	}

	/**
	 * @return	RouteAccess
	 */
	protected function getRouteAccess()
	{
		return $this->access;
	}

	/**
	 * @return	RouteAccess
	 */
	protected function getRouteView()
	{
		return $this->view;
	}

	/**
	 * @return	RouteIntercept
	 */
	protected function getRouteIntercept()
	{
		return $this->intercept;
	}

	/**
	 * @return	RouteStartup
	 */
	public function getRouteStartup()
	{
		return $this->startup;
	}

	/**
	 * @return	RouteAccess
	 */
	protected function createRouteAccess()
	{
		return new RouteAccess();
	}

	/**
	 * @return	RouteView
	 */
	protected function createRouteView()
	{
		return new RouteView();
	}

	/**
	 * @return	RouteIntercept
	 */
	protected function createRouteIntercept()
	{
		return new RouteIntercept();
	}

	/**
	 * @return	RouteStartup
	 */
	protected function createRouteStartup()
	{
		return new RouteStartup();
	}

	/**
	 * @return	RouteAction
	 */
	protected function createRouteAction()
	{
		return new RouteAction();
	}
}
