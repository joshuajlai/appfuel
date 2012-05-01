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
namespace Appfuel\Kernel\Mvc;

use InvalidArgumentException,
    Appfuel\DataStructure\Dictionary;

/**
 */
class MvcRouteDetail extends Dictionary implements MvcRouteDetailInterface
{
	/**
	 * Flag used to determine if this route is public and reqiures no acl check
	 * @var	 bool
	 */
	protected $isPublic = false;

	/**
	 * Class name of the mvc action used by the dispatching system
	 * @var string
	 */
	protected $actionName = 'Controller';

	/**
	 * Fully qualified namespace of the mvc action. By default this is the
	 * combination of namespace and actionName 
	 * @var string
	 */
	protected $actionClass = '';

	/**
	 * Flag used to detemine if the controller used by this route is internal.
	 * Internal routes can not be executed by the front controller and thus
	 * inaccessible from the outside
	 * @var bool
	 */
	protected $isInternal = false;

	/**
	 * List of acl codes allowed to access this route
	 * @var	array
	 */
	protected $aclCodes = array();

	/**
	 * Used when isPublic is false but you want anyone who as acl access to
	 * pass through
	 * @var bool
	 */
	protected $isIgnoreAcl = false;

	/**
	 * List of startup tasks to exclude or include
	 * @var array
	 */
	protected $startup = array(
		'is-prepend' => false,
		'is-ignore-config' => false,
		'exclude' => array(),
		'include' => array()
	);

	/**
	 * List of intercepting filters used by the front controller for 
	 * this route
	 * @var array
	 */
	protected $filters = array(
		'pre' => array(
			'is-skip' => false,
			'include' => array(),
			'exclude' => array()
		),
		'post' => array(
			'is-skip' => false,
			'include' => array(),
			'exclude' => array()
		)
	);

	/**
	 * Determines if the view is considered when processing the mvc action.
	 * @var	bool
	 */
	protected $isView = true;

	/**
	 * Determines if the framework needs to compose the view from the view data
	 * @var bool
	 */
	protected $isManualView = false;

	/**
	 * This string will represent the complete view
	 * @var string 
	 */	
	protected $rawView = null;

	/**
	 * Name of the view package which represents the view for this route.
	 * View packages are generally html pages
	 * @var string
	 */
	protected $viewPkg = null;

	/**
	 * Holds custom parameters needed for manually build views or view data
	 * @var array
	 */
	protected $viewParams = array();

	/**
	 * @param	array	$data
	 * @return	MvcRouteDetail
	 */
	public function __construct(array $data)
	{
		if (isset($data['is-public']) && true === $data['is-public']) {
			$this->isPublic = true;
		}

		if (isset($data['is-internal']) && true === $data['is-internal']) {
			$this->isInternal = true;
		}

		if (isset($data['acl-access'])) {
			$this->setAclCodes($data['acl-access']);
		}
	
		if (isset($data['is-ignore-acl']) && true === $data['is-ignore-acl']) {
			$this->isIgnoreAcl = true;
		}
	
		if (isset($data['intercept'])) {
			$this->setInterceptingFilters($data['intercept']);
		}
	
		if (isset($data['startup'])) {
			$this->setStartup($data['startup']);
		}

		if (isset($data['is-view']) && false === $data['is-view']) {
			$this->disableView();
		}

		if (isset($data['is-manual-view']) && 
			true === $data['is-manual-view']) {
			$this->enableManualView();
		}

		if (isset($data['raw-view'])) {
			$this->setRawView($data['raw-view']);
		}

		if (isset($data['view-pkg'])) {
			$this->setViewPackage($data['view-pkg']);
		}

		if (isset($data['view-params'])) {
			$this->setViewParams($data['view-params']);
		}

		$params = array();
		if (isset($data['params']) && is_array($data['params'])) {
			$params = $data['params'];
		}

		if (! isset($data['action-name'])) {
			$err  = "action name must be defined. This is the class name of ";
			$err .= "mvc action used by this route. key is -(action-name)";
			throw new InvalidArgumentException($err);
		}
		$this->setActionName($data['action-name']);

		if (isset($data['action-class'])) {
			$this->setActionClass($class);
		}

		parent::__construct($params);
	}

	/**
	 * @return	bool
	 */
	public function isPublicAccess()
	{
		return $this->isPublic;
	}

	/**
	 * @return	bool
	 */
	public function isInternalOnlyAccess()
	{
		return $this->isInternal;
	}

	/**
	 * @return bool
	 */
	public function isIgnoreAcl()
	{
		return $this->isIgnoreAcl;
	}

	/**
	 * @param	string	$code
	 * @return	bool
	 */
	public function isAccessAllowed($codes)
	{
		if ($this->isPublicAccess() || $this->isIgnoreAcl()) {
			return true;
		}
		
		if (is_string($codes)) {
			$codes = array($codes);
		}
		else if (! is_array($codes)) {
			return false;
		}

		$compare = array();
		foreach ($codes as $code) {
			if (is_string($code) && ! empty($code)) {
				$compare[] = $code;
			}
		}

		$result = array_intersect($this->aclCodes, $compare);
		if (empty($result)) {
			return false;
		}

		return true;
	}

	/**
	 * @return	bool
	 */
	public function isSkipPreFilters()
	{
		return $this->filters['pre']['is-skip'];
	}

	/**
	 * @return	bool
	 */
	public function isPreFilters()
	{
		$list = $this->filters['pre']['include'];
		return is_array($list) && ! empty($list); 
	}

	/**
	 * @return	array
	 */
	public function getPreFilters()
	{
		return $this->filters['pre']['include'];
	}

	/**
	 * @return	bool
	 */
	public function isExcludedPreFilters()
	{
		$list = $this->filters['pre']['exclude'];
		return is_array($list) && ! empty($list); 
	}

	/**
	 * @return	array
	 */
	public function getExcludedPreFilters()
	{
		return $this->filters['pre']['exclude'];
	}

	/**
	 * @return	bool
	 */
	public function isSkipPostFilters()
	{
		return $this->filters['post']['is-skip'];
	}

	/**
	 * @return	bool
	 */
	public function isPostFilters()
	{
		$list = $this->filters['post']['include'];
		return is_array($list) && ! empty($list); 
	}

	/**
	 * @return array
	 */
	public function getPostFilters()
	{
		return $this->filters['post']['include'];
	}

	/**
	 * @return	bool
	 */
	public function isExcludedPostFilters()
	{
		$list = $this->filters['post']['exclude'];
		return is_array($list) && ! empty($list); 
	}

	/**
	 * @return array
	 */
	public function getExcludedPostFilters()
	{
		return $this->filters['post']['exclude'];
	}

	/**
	 * @return	bool
	 */
	public function isIgnoreConfigStartupTasks()
	{
		return $this->startup['is-ignore-config'];
	}

	public function isPrependStartupTasks()
	{
		return $this->startup['is-prepend'];
	}

	/**
	 * @return	bool
	 */
	public function isStartupTasks()
	{
		return ! empty($this->startup['include']);
	}

	/**
	 * @return	array
	 */
	public function getStartupTasks()
	{
		return $this->startup['include'];
	}

	/**
	 * @return	bool
	 */
	public function isExcludedStartupTasks()
	{
		return ! empty($this->startup['exclude']);
	}

	/**
	 * @return	array
	 */
	public function getExcludedStartupTasks()
	{
		return $this->startup['exclude'];
	}

	/**
	 * @return	bool
	 */
	public function isView()
	{
		return $this->isView;
	}

	/**
	 * @return	bool
	 */
	public function isManualView()
	{
		return $this->isManualView;
	}

	/**
	 * @return	bool
	 */
	public function isRawView()
	{
		return is_string($this->rawView);
	}

	/**
	 * @return	string
	 */
	public function getRawView()
	{
		return $this->rawView;
	}

	/**
	 * @return	bool
	 */
	public function isViewPackage()
	{
		return is_string($this->viewPkg) && ! empty($this->viewPkg);
	}

	/**
	 * @return	string
	 */
	public function getViewPackage()
	{
		return $this->viewPkg;
	}

	/**
	 * @return	array
	 */
	public function getViewParams()
	{
		return $this->viewParams;	
	}

	/**
	 * @return	string
	 */
	public function getActionName()
	{
		return $this->actionName;
	}

	/**
	 * @return	string
	 */
	public function getActionClass()
	{
		return $this->actionClass;
	}

	/**
	 * @param	mixed string | array
	 * @return	null
	 */
	protected function setAclCodes($codes)
	{
		if (is_string($codes)) {
			$codes = array($codes);
		}
		else if (! is_array($codes)) {
			$err = 'acl codes must be a string or an array of strings';
			throw new InvalidArgumentException($err);
		}

		foreach ($codes as $code) {
			if (! is_string($code)) {
				$err = 'invalid acl code, all codes must be strings';
				throw new InvalidArgumentException($err);
			}
		}

		$this->aclCodes = $codes;
	}

	/**
	 * @param	array	$data
	 * @return	null
	 */
	protected function setInterceptingFilters(array $data)
	{
		if (isset($data['is-skip-pre']) && true === $data['is-skip-pre']) {
			$this->filters['pre']['is-skip'] = true;
		}

		if (isset($data['include-pre'])) {
			$pre = $this->filterData($data['include-pre'], 'include-pre');
			$this->filters['pre']['include'] = $pre;
		}

		if (isset($data['exclude-pre'])) {
			$pre = $this->filterData($data['exclude-pre'], 'exclude-pre');
			$this->filters['pre']['exclude'] = $pre;
		}


		if (isset($data['is-skip-post']) && true === $data['is-skip-post']) {
			$this->filters['post']['is-skip'] = true;
		}

		if (isset($data['include-post'])) {
			$post = $this->filterData($data['include-post'], 'include-post');
			$this->filters['post']['include'] = $post;
		}

		if (isset($data['exclude-post'])) {
			$post = $this->filterData($data['exclude-post'], 'exclude-post');
			$this->filters['post']['exclude'] = $post;
		}
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */
	protected function setStartup(array $data)
	{
		if (isset($data['include']) && is_array($data['include'])) {
			$list = $data['include'];
			foreach ($list as $task) {
				if (! is_string($task) || empty($task)) {
					$err = 'startup task to include must be a non empty string';
					throw new InvalidArgumentException($err);
				}
			}

			$this->startup['include'] = $list;
		}

		if (isset($data['exclude']) && is_array($data['exclude'])) {
			$list = $data['exclude'];
			foreach ($list as $task) {
				if (! is_string($task) || empty($task)) {
					$err = 'startup task to exclude must be a non empty string';
					throw new InvalidArgumentException($err);
				}
			}

			$this->startup['exclude'] = $list;
		}

		if (isset($data['is-ignore-config']) &&
			true === $data['is-ignore-config']) {
			$this->startup['is-ignore-config'] = true;
		}

		if (isset($data['is-prepend']) && true === $data['is-prepend']) {
			$this->startup['is-prepend'] = true;
		}
	}

	/**
	 * @param	array	$data
	 * @param	string	$type	array key where failure occured
	 * @return	array
	 */
	protected function filterData($data, $type)
	{
		if (is_string($data)) {
			$data = array($data);
		}
		elseif (! is_array($data)) {
			$err  = "invalid data structure for -($type), must be ";
			$err .= "a string or an array of strings";
			throw new InvalidArgumentException($err);
		}

		foreach ($data as $filter) {
			if (! is_string($filter) || empty($filter)) {
				$err  = 'invalid filter in list, all filters in the ';
				$err .= 'array must be non empty strings';
				throw new InvalidArgumentException($err);
			}			
		}

		return $data;
	}

	/**
	 * @return	null
	 */
	protected function disableView()
	{
		$this->isView = false;
	}

	/**
	 * @return	null
	 */
	protected function enableManualView()
	{
		$this->isManualView = true;
	}

	/**
	 * @param	string | object $view
	 * @return	null
	 */
	protected function setRawView($view)
	{
		if (! is_string($view) && 
			! (is_object($view) && is_callable(array($view, '__toString')))) {
			$err  = "raw view must be a string or an object that implements ";
			$err .= "__toString";
			throw new InvalidArgumentException($err);
		}

		$this->rawView =(string) $view;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setViewPackage($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = "package name must be non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->viewPkg = $name;
	}
	
	/**
	 * @param	array $params
	 * @return	null
	 */
	protected function setViewParams(array $params)
	{
		$this->viewParams = $params;
	}

	/**
	 * @param	string	$ns
	 * @return	null
	 */
	protected function setActionName($className)
	{
		if (! is_string($className)) {
			$err = 'action class name must be a string';
			throw new InvalidArgumentException($err);
		}

		$this->actionName = $className;
	}

	/**
	 * @param	string	$ns
	 * @return	null
	 */
	protected function setActionClass($qualifiedNs)
	{
		if (! is_string($qualifiedNs) || empty($qualifiedNs)) {
			$err  = 'the fully qualified namespace of an mvc action ';
			$err .= 'must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		$this->actionClass = $qualifiedNs;
	}
}
