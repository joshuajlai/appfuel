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


use Exception,
	Appfuel\Http\HttpOutputAdapter,
	Appfuel\Console\ConsoleOutputAdapter,
	Appfuel\Framework\App\Context\ContextInterface,
	Appfuel\Framework\Output\OutputEngineInterface,
	Appfuel\Framework\Output\AdapterHeaderInterface,
	Appfuel\Framework\Output\EngineAdapterInterface;

/**
 * The ouput engine is responsible for rendering content to a given output
 * strategy. There are two output strategies console and http which handle
 * all rendering details. The engine delagates to the output adapter and thus
 * does not have knowledge of any given strategy. 
 */
class OutputEngine implements OutputEngineInterface
{
	/**
	 * Adapter used to render or build the output
	 * @var	AdapterInterface
	 */
	protected $adapter = null;

	/**
	 * List of adapter configuration, like http headers
	 * @var array
	 */
	protected $configuration = array();

	/**
	 * @param	OutputAdapterInterface $adapter
	 * @return	OutputEngine
	 */
	public function __construct(OutputAdapterInterface $adapter)
	{
		$this->setAdapter($adapter);
	}

	/**
	 * @param	OuputAdapterInterface	$adapter
	 * @return	null
	 */
	public function setAdapter(OutputAdapterInterface $adapter)
	{
		$this->adapter = $adapter;
	}

	/**
	 * @return	EngineAdapterInterface
	 */
	public function getAdapter()
	{
		return $this->adapter;
	}

	/**
	 * @param	string	$item
	 * @return	null
	 */
	public function addConfiguration($item)
	{
		if (empty($item) || !is_string($item)) {
			$err = 'configuration item must be a non empty string';
			throw new InvalidArgumentException($err);
		}
		$this->configuration[] = $item;
	}

	/**
	 * @return	array
	 */
	public function getConfiguration()
	{
		return $this->configuration;
	}

	/**
	 * @param	mixed	$data
	 * @return	null
	 */
	public function render($data)
	{
		$adapter = $this->getAdapter();
		if ($this->isConfiguration()) {
			$adapter->loadConfiguration($this->getConfiguration());
		}

		return $adapter->output($data);
	}

	/**
	 * @param	string	$msg
	 * @return	null
	 */
	public function renderError($msg)
	{
		$adapter = $this->getAdapter();
		if ($this->isConfiguration()) {
			$adapter->loadConfiguration($this->getConfiguration());
		}

		return $adapter->renderError($msg);
	}
}
