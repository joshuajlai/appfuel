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
namespace Appfuel\Framework;

use Appfuel\Stdlib\Data\BagInterface,
	Appfuel\Stdlib\Data\Bag;

/**
 * tba
 */
class Message implements MessageInterface
{
    /**
     * Data structure used to hold info object in the message
     * @var BagInterface
     */
    protected $bag = NULL;

    /**
     * Route
     * Outside of the bag to protected it from being overwriten
     * @var RouteInterface
     */
    protected $route = NULL;

    /**
     * @var  RequestInterface
     */
    protected $request = NULL;

    /**
     * @param   mixed   $data   
     * @return  Message
     */
    public function __construct($data = NULL)
    {
        /*
         * check is there is an array data with the initialization or if
         * a Bag structure is manually being passed in otherwise create an 
         * empty bag
         */
        if (is_array($data)) {
            $bag = new Bag($data);
        } else if ($data instanceof BagInterface) {
            $bag = $data;
        } else {
            $bag = new Bag();
        }

        $this->bag = $bag;
    }

    /**
     * Add data to the payload for use by destination
     * 
     * @param   string  $key    to identity data
     * @param   mixed   $value  
     * @return  Message
     */
    public function add($key, $value)
    {
        $this->getBag()->add($key, $value);
        return $this;
    }

    /**
     * @param   string  $key    
     * @param   mixed   $default    returned when not found
     * @return  mixed
     */
    public function get($key, $default = NULL)
    {
        return $this->getBag()->get($key, $default);
    }

    /**
     * @param   string  $key
     * @return  bool
     */
    public function exists($key)
    {
        $this->getBag()->exists($key);
    }

    /**
     * The route this message is dispatched to
     * 
     * @return  Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param   Route
     * @return  Message
     */
    public function setRoute(RouteInterface $route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRoute()
    {
        return $this->route instanceof RouteInterface;
    }

    /**
     * @return  \Wiredrive\App\Request\RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param   RequestInterface    $request
     * @return  Command
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRequest()
    {
        return $this->request instanceof RequestInterface;
    }

    /**
     * @return  Message
     */
    public function __clone()
    {
        $this->route   = NULL;
        $this->request = NULL;
        $this->bag     = new Bag();
    }

    /**
     * @return Bag
     */
    protected function getBag()
    {
        return $this->bag;
    }
}
