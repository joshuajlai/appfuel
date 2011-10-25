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
namespace Appfuel\MsgBroker\Amqp;

use Appfuel\Framework\Exception,
	Appfuel\Framework\MsgBroker\Amqp\ConsumerDetailInterface;

/**
 */
class ConsumerDetail implements ConsumerDetailInterface
{
    /**
     * Datastructure to hold the info required to declare an exchange
     * @var string
     */
    protected $data = array(
        'queue'			=> '',
        'consumer-tag'  => 'direct',
        'no-local'      => false,
        'no-ack'		=> false,
        'exclusive'		=> true,
        'no-wait'		=> false,
        'callback'      => null,
        'ticket'        => null
    );

	/**
     * @param   array   $data
	 * @return	ConsumerDetail
	 */
	public function __construct(array $data)
	{
        $this->setData($queue);

	}

    /**
     * @return  array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return array_values($this->data);
    }
}
