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
			$this->isInternal = true;;
		}

		if (isset($data['acl-access'])) {
			$this->setAclCodes($data['acl-access']);
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
	 * @param	string	$code
	 * @return	bool
	 */
	public function isAccessAllowed($codes)
	{
		if ($this->isPublicAccess()) {
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
}
