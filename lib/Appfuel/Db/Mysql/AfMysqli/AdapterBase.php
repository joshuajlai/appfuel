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
namespace Appfuel\Db\Mysql\AfMysqli;

use mysqli,
	Exception,
	Appfuel\Db\DbResponse,
	Appfuel\Db\DbError,
	Appfuel\Framework\Db\DbErrorInterface;

/**
 * Mysqli adapter exposes the mysqli functionality though the
 * the adapter interface
 */
class AdapterBase 
{
	/**
	 * @var	mysqli
	 */
	protected $driver = null;

	/**
	 * @param	mysqli $driver
	 * @return	AdapterBase
	 */
	public function __construct(mysqli $driver)
	{
		$this->driver = $driver;
	}

	/**
	 * @return	mysqli
	 */
	public function getDriver()
	{
		return $this->driver;
	}

	/**
	 * @param	string	$code
	 * @param	string	$text
	 * @param	string	$sqlState
	 * @return	DbError
	 */
	public function createError($code, $text, $sqlState = null)
	{
		return new DbError($code, $text, $sqlState);
	}

    /**
     * Create a DbResponse depending on the type of data.
     * When data is false 
     */
    public function createResponse($data = null)
    {  
        $response = null;
        if (null === $data) {
            $response =  new DbResponse();
        }
        else if (is_array($data)) {
            $response = new DbResponse($data);
        }
		else if ($data instanceof DbResponse) {
			return $data;
		}
		else if ($data instanceof Exception) {
			$error = $this->createError(
				$data->getCode(), 
				$data->getMessage()
			);

			$response = new DbResponse(null, $error);
		}
        else if ($data instanceof DbErrorInterface) {
            $response = new DbResponse(null, $data);
        }

        return $response;
    }
}
