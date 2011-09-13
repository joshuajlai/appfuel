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
namespace Appfuel\App;


use Appfuel\Framework\Exception,
	Appfuel\Http\HttpOutputAdapter,
	Appfuel\Console\ConsoleOutputAdapter,
	Appfuel\Framework\Context\ContextInterface,
	Appfuel\Framework\Output\OutputEngineInterface,
	Appfuel\Framework\Output\AdapterHeaderInterface,
	Appfuel\Framework\Output\EngineAdapterInterface;

/**
 * The render engine is responsible for outputting data or building the data
 * data into string. There are two basic strategies for output: html or cli
 * because you are either accessing data from the web or on the servers command
 * line.
 */
class OutputEngine implements OutputEngineInterface
{
	/**
	 * Adapter used to render or build the output
	 * @var	AdapterInterface
	 */
	protected $adapter = null;

	/**
	 * @param	string	$type
	 * @return	OutputEngine
	 */
	public function __construct($type = null)
	{
		if (null == $type) {
			if (! defined('AF_APP_TYPE')) {
				throw new Exception("constant AF_APP_TYPE must be defined");
			}
			$adapter = $this->createAdapter(AF_APP_TYPE);
		}
		else if (! empty($type) && is_string($type)) {
			$adapter = $this->createAdapter($type);
		}
		else if ($type instanceof EngineAdapterInterface) {
			$adapter = $type;
		}

		$this->setAdapter($adapter);
	}

	/**
	 * @param	EngineAdapterInterface	$adapter
	 * @return	null
	 */
	public function setAdapter(EngineAdapterInterface $adapter)
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
	 * @param	ContextInterface	$context
	 * @return	null
	 */
	public function render(ContextInterface $context)
	{
		$adapter = $this->getAdapter();
		/*
		 * used mainly by http adapters for the reponse headers
		 */
		if ($adapter instanceof AdapterHeaderInterface) {
			$headers = $context->get('output-headers', array());
			
			if (! empty($headers) && is_array($headers)) {
				$adapter->addResponseHeaders($headers);
			}
		}

		return $adapter->output($context->getOutput());
	}

	/**
	 * @param	string	$type
	 * @return	OutputEngineAdapterInterface
	 */
	protected function createAdapter($type)
	{
		$err = 'Could not create output adapter:';
		if (empty($type) || ! is_string($type)) {
			throw new Exception("$err param type must be a non empty string");
		}

		$httpTypes = array('app-page', 'app-api', 'app-service');
		if ('app-cli' === $type) {
			return = new ConsoleOutputAdapter();
		}
		else if (in_array($type, $httpTypes)) {
			return = new HttpOutputAdapter();
		}
		else {
			$err .= " Only support adapters for the following application";
			$err .= " types: app-page, app-service, app-api and app-cli";
			throw new Exception($err);
		}
	}
}
