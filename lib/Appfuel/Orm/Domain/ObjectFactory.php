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
namespace Appfuel\Orm\Domain;

use Appfuel\Framework\Registry,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Orm\Domain\ObjectFactoryInterface,
	Appfuel\Framework\Orm\Domain\MappedObjectNotFoundException;

/**
 * The object factory is resposible for converting domain keys into domain 
 * objects. It is not responsible for marshalling data into those objects.
 */
class ObjectFactory implements ObjectFactoryInterface
{
	/**
	 * @param	string	$key
	 * @return	mixed
	 */
	public function createDomainObject($key, $isDomain = true)
	{
		$map = Registry::get('domain-keys', false);
		if (! $map) {
			return false;
		}

		if (! is_array($map) || ! array_key_exists($key, $map)) {
			return false;
		}
		$namespace = $map[$key];

		if (! $isDomain) {
			return new $namespace();
		}

		$pos = strrpos($namespace, '\\');
		if (0 === $pos || false === $pos) {
			$qualified= $namespace . 'Model';
		} else {
			$class = substr($namespace, $pos + 1);
			$class = $class . 'Model';
			$qualified = "$namespace\\$class";
		}

		try {
			return new $qualified();
		} catch (\Exception $e) {
			$err = "object not found for ($key) at ($qualified)";
			throw new MappedObjectNotFoundException($err, 0, $e);
		}
	}
}
