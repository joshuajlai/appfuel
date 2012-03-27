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
	 * Namepsace of the mvc action
	 * @var string
	 */
	protected $namespace = '';

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
	 * A list of parameters used to initialize the mvc action. Usually a list
	 * of domain namespaces
	 * @var array
	 */
	protected $actionInit = array();

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
	 * @var	array
	 */
	protected $viewDetail = null;

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

		if (isset($data['view-detail'])) {
			$viewDetail = $data['view-detail'];
			if (is_array($viewDetail)) {
				$this->loadViewDetail($viewDetail);
			}
			else if ($viewDetail Instanceof MvcViewDetailInterface) {
				$this->setViewDetail($viewDetail);
			}
		}

		$params = array();
		if (isset($data['params']) && is_array($data['params'])) {
			$params = $data['params'];
		}

		if (isset($data['namespace'])) {
			$this->setNamespace($data['namespace']);
		}

		if (isset($data['action-name'])) {
			$this->setActionName($data['action-name']);
		}

		if (isset($data['action-class'])) {
			$class = $data['action-class'];
		}
		else {
			$class = "{$this->getNamespace()}\\{$this->getActionName()}";
		}
		$this->setActionClass($class);

		if (isset($data['action-init'])) {
			$this->setActionInit($data['action-init']);
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
	 * @return	array
	 */
	public function getViewDetail()
	{
		return $this->viewDetail;
	}

	/**
	 * @return	bool
	 */
	public function isViewDetail()
	{
		return $this->viewDetail instanceof MvcViewDetailInterface;
	}

	/**
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->namespace;
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
	 * @return	array
	 */
	public function getActionInit()
	{
		return $this->actionInit;
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
	 * @param	array	$data
	 * @return	null
	 */
	protected function loadViewDetail(array $data)
	{
		$this->setViewDetail($this->createViewDetail($data));
	}

	/**
	 * @param	MvcViewDetailInterface $detail
	 * @return	null
	 */
	protected function setViewDetail(MvcViewDetailInterface $detail)
	{
		$this->viewDetail = $detail;
	}

	/**
	 * @param	array	$data
	 * @return	MvcViewDetail
	 */
	protected function createViewDetail(array $data)
	{
		return new MvcViewDetail($data);
	}

	/**
	 * @param	string	$ns
	 * @return	null
	 */
	protected function setNamespace($ns)
	{
		if (! is_string($ns)) {
			$err = 'namespace must be a string';
			throw new InvalidArgumentException($err);
		}

		$this->namespace = $ns;
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

	/**
	 * @param	array	$list
	 * @return	null
	 */
	public function setActionInit(array $params)
	{
		$this->actionInit = $params;
	}
}
