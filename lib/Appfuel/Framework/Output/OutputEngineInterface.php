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
namespace Appfuel\Framework\Output;

use Appfuel\Framework\App\Context\ContextInterface;

/**
 * Appfuel Framework Exception
 */
interface OutputEngineInterface
{
	public function getAdapter();
	public function setAdapter(EngineAdapterInterface $adapter);
	public function renderContext(ContextInterface $context);
	public function renderRaw($data);
}
