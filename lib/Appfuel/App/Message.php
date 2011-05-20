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
namespace Appfuel\App;

use Appfuel\Framework\MessageInterface,
    Appful\Framework\App\Route\RouteInterface,
    Appfuel\Framework\Request\RequestInterface,
    Appfuel\Framework\View\ViewInterface,
    Appfuel\Stdlib\Data\Dictionary;

/*n
 * Message is a specialized disctionary used to pass throught the dispatch
 * system and into the action controllers. It allows the framework to inject
 * all the necessary objects into the action controllers and lets the 
 * controller pass back the document and any other meta data 
 */
class Message extends Dictionary implements MessageInterface
{
	/**
     * Flag used to indicate to the render engine if the document
     * should be rendered to output
     * @var bool
     */
    protected $isDocRender = true;

    /**
     * @return  bool
     */
    public function isDocRender()
    {
        return $this->isDocRender;
    }

    /**
     * @return Message
     */
    public function disableDocRender()
    {
        $this->isDocRender = false;
        return $this;
    }

    /**
     * @return  Message
     */
    public function enableDocRender()
    {
        $this->isDocRender = true;
        return $this;
    }

    /**
     * Indicates that a route is set and uses a route interface
     * 
     * @return bool
     */
    public function isRoute()
    {
        if (isset($this->items['route']) &&
            $this->items['route'] instanceof RouteInterface) {
            return true;
        }

        return false;
    }

    /**
     * Indicates that a request is set and uses a request interface
     * 
     * @return bool
     */
    public function isRequest()
    {
        if (isset($this->items['request']) &&
            $this->items['request'] instanceof RequestInterface) {
            return true;
        }

        return false;
    }

    /**
     * Indicates that a client is set and uses a client domain
     * 
     * @return bool
     */
    public function isDoc()
    {
        if (isset($this->items['doc']) &&
            $this->items['doc'] instanceof ViewInterface) {
            return true;
        }

        return false;
    }
}
