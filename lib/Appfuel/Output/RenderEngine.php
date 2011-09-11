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
namespace Appfuel\Output;


use Appfuel\Framework\Exception,
	Appfuel\Framework\Context\ContextInterface,
	Appfuel\Framework\Output\AdapterInterface,
	Appfuel\Framework\Output\RenderEngineInterface;

/**
 * The render engine is responsible for outputting data or building the data
 * data into string. There are two basic strategies for output: html or cli
 * because you are either accessing data from the web or on the servers command
 * line.
 */
class RenderEngine implements RenderEngineInterface
{
	/**
	 * Adapter used to render or build the output
	 * @var	AdapterInterface
	 */
	protected $adapter = null;

	/**
	 * @param	string	$type
	 * @return	RenderEngine
	 */
	public function __construct($type)
	{
		if (empty($type) || ! is_string($type)) {
			throw new Exception("Adapter type must be a non empty string");
		}

		$this->adapter = $this->createAdapter($type);
	}

	/**
	 * @return	EngineAdapterInterface
	 */
	public function getAdapter()
	{
		return $this->adapter;
	}

	/**
	 * @param	string	$type
	 * @return	EngineAdapterInterface
	 */
	protected function createAdapter($type)
	{
		if ('html' === $type) {
			return new HtmlAdapter();
		}
		else if ('cli' === $type) {
			return new CliAdapter();
		}

		throw new Exception("Adapter type must be html or cli");
	}
}
