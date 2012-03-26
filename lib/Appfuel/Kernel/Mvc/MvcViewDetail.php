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

use InvalidArgumentException;

/**
 * Value object used to describe how to build a view interface
 */
class MvcViewDetail implements MvcViewDetailInterface
{
	/**
	 * Flag used to determine if a view needs to be built
	 * @var	 bool
	 */
	protected $isView = true;

	/**
	 * The view strategy is a string used to classify the type of view. Appfuel
	 * supports five strategies: html-page, html, ajax, console, general
	 * @var string
	 */
	protected $strategy = 'general';

	/**
	 * List of parameters passed into any of the view builders build methods
	 * @var	array
	 */
	protected $params = array();

	/**
	 * When present the view build will use this method instead of its default
	 * method to build the view. This method must the following two params:
	 * (string) strategy, (array) params
	 * @var array
	 */
	protected $method = null;

	/**
	 * Appfuel will create this class instead of trying to build one of its
	 * own
	 * @var string
	 */
	protected $viewClass = null;

	/**
	 * Used when the view is only a simple string
	 * @var string
	 */
	protected $raw = null;

	/**
	 * @param	array	$data
	 * @return	MvcRouteDetail
	 */
	public function __construct(array $data)
	{
		
		if (isset($data['is-view']) && false === $data['is-view']) {
			$this->isView = false;
		}

		if (isset($data['strategy']) && is_string($data['strategy'])) {
			$this->strategy = $data['strategy'];
		}

		if (isset($data['params']) && is_array($data['params'])) {
			$this->params = $data['params'];
		}
		
		if (isset($data['method']) && 
			(is_string($data['method']) || is_callable($data['method']))) {
			$this->method = $data['method'];
		}

		if (isset($data['view-class'])) {
			$class = $data['view-class'];
			if (! is_string($class) && ! empty($class)) {
				$err = 'view class must be a non empty string';
				throw new InvalidArgumentException($err);
			}

			$this->viewClass = $data['view-class'];
		}

		if (isset($data['raw']) && is_string($data['raw'])) {
			$this->raw = $data['raw'];
		}
	}

	/**
	 * @return	bool
	 */
	public function isView()
	{
		return $this->isView;
	}

	/**
	 * @return	string
	 */
	public function getStrategy()
	{
		return $this->strategy;
	}

	/**
	 * @return	array
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * @return	bool
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * @return string
	 */
	public function getViewClass()
	{
		return $this->viewClass;
	}

	/**
	 * @return	bool
	 */
	public function isViewClass()
	{
		return is_string($this->viewClass) && ! empty($this->viewClass);
	}

	/**
	 * @return	bool
	 */
	public function isRawView()
	{
		return is_string($this->raw);
	}

	/**
	 * @return	string
	 */
	public function getRawView()
	{
		return $this->raw;
	}
}
