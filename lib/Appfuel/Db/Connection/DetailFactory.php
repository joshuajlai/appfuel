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
namespace Appfuel\Db\Connection;

use Appfuel\Data\Dictionary,
	Appfuel\Framework\Exception,
	Appfuel\Framework\Db\Connection\DetailFactoryInterface,
	Appfuel\Framework\Db\Connection\ParserInterface;


/**
 * Parse a connection string into it's individual components
 */
class DetailFactory implements DetailFactoryInterface
{
	/**
	 * Used to parse the connection string for key value pairs
	 * @var ParserInterface
	 */
	protected $parser = null;

	/**
	 * @param	ParserInterface $parser
	 * @return	DetailFactory
	 */
	public function __construct(ParserInterface $parser = null)
	{
		if (null === $parser) {
			$parser = new Parser();
		}

		$this->parser = $parser;
	}

	/**
	 * @return	ParserInterface
	 */
	public function getParser()
	{
		return $this->parser;
	}

	/**
	 * @throws	Appfuel\Framework\Exception		by ConnectionDetail
	 * @param	string	$connectionString
	 * @return	ConnectionDetail | false on failure
	 */
	public function createConnectionDetail($connectionString)
	{
		$parser = $this->getParser();
		$result = $parser->parse($connectionString);
		if (false === $result) {
			return false;
		}

		/* required parameters for a successful connection to a vendor
		 * specific adapter
		 */
		$vendor   = $result->get('vendor');
		$adapter  = $result->get('adapter');
		$host     = $result->get('host');
		$dbName   = $result->get('dbname');
		$username = $result->get('username');
		$password = $result->get('password');

		$detail = new ConnectionDetail($vendor, $adapter);
		$detail->setHost($host)
			   ->setDbName($dbName)
			   ->setUsername($username)
			   ->setPassword($password);

		$port   = $result->get('port');
		$socket = $result->get('socket');		
		if (null !== $port) {
			$detail->setPort($port);
		}

		if (null !== $socket) {
			$detail->setSocket($socket);
		}

		return $detail;
	}
}
