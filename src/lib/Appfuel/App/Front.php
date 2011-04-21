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
	Appfuel\Framework\FrontControllerInterface,
	Appfuel\Framework\DispatchInterface,
    Appfuel\Framework\MessageInterface,
    Appfuel\Framework\RenderInterface,
    Appfuel\Framework\Doc\DocumentInterface;

/**
 * Handle dispatching the request and outputting the response
 */
class Front implements FrontControllerInterface
{
    /**
     * System used to manage action commands
     * @var DispatchInterface
     */
    protected $dispatcher = NULL;

    /**
     * System used to render output
     * @var RenderInterface
     */
    protected $engine = NULL;

    /**
     * Constructor
     * Assign server which is an immutable member
     *
     * @param   Dispatch    $dispatcher
     * @param   Output      $output
     * @return  Front
     */
    public function __construct(DispatchInterface $dispatcher, 
								RenderInterface $engine)
    {
        $this->dispatcher   = $dispatcher;
        $this->renderEngine = $engine;
    }

    /**
     * @return  Dispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @return  Output
     */
    public function getRenderEngine()
    {
        return $this->engine;
    }

    /**
     * Dispatch
     * Use the route destination to create the controller and execute the
     * its method. Check the return of method, if its a message with a 
     * distination different from the previous then dispath that one
     *
     * @param   MessageInterface $msg
     * @return  MessageInterface
     */
    public function dispatch(MessageInterface $msg)
    {
        $dispatcher = $this->getDispatcher();
        $controller = $dispatcher->load($msg);

        try {
            $msg = $dispatcher->execute($controller, $msg);
        } catch (Exception $e) {
            $format = $msg->get('responseType', 'html');
            $route  = Factory::createErrorRoute();

            $txt    = $e->getMessage();
            $code   = $e->getCode();
            $msg->add('errorMsg',  $txt)
                ->add('errorCode', $code);

            $msg->add('route', $route)
                ->add('doc', null);

            $controller = $dispatcher->load($msg);
            $msg = $dispatcher->execute($controller, $msg);
        }

        return $msg;
    }

    /**
     * Render output. Can either echo the built content or
     * return it as a string
     *
     * @param   MessageInterface    $msg   
     * @return  mixed  void|string
     */
    public function render(MessageInterface $msg)
    {
        $engine = $this->getRenderEngine();

        if (! $msg->isDoc()) {
            return false;
        }

        $doc = $msg->get('doc');
        if (! $msg->isDocRender()) {
            return $engine->build($doc);
        }

        $engine->render($doc);
    }
}
