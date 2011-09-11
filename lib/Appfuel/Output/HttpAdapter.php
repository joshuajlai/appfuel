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
	Appfuel\Framework\Output\AdapterEngineInterface;

/**
 * Handle specific details for outputting data to the commandline
 */
class HttpAdapter implements AdapterEngineInterface
{
    /**
     * Render to the command line or build into a string
     * 
     * @param   mixed   $data
     * @param   string  $strategy
     * @return  mixed
     */
    public function output($data, $strategy = 'render')
    {  
        if ('render' === $strategy) {
            $result = $this->render();
        } else {
            $result = $this->build();
        }

        return $result;
    }

    /**
     * @param   mixed   $data
     * @return  string
     */
    public function build($data)
    {  
        if (! $this->isValidOutput($data)) {
            return '';
        }

        if (is_scalar($data)) {
            return $data;
        }

        return $data->__toString();
    }


    /**
     * Render output to the stdout.
     * 
     * @param   mixed   $data
     * @return  null
     */
    public function render($data)
    {  
        if (! $this->isValidOutput($data)) {

        }

        if (is_object($data)) {
        }

    }
}
