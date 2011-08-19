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
namespace Appfuel\App\Render;


use Appfuel\Framework\Exception,
	Appfuel\Framework\RenderInterface,
	Appfuel\Framework\MessageInterface,
	Appfuel\Framework\Doc\DocInterface;

/**
 *
 */
class Engine implements RenderInterface
{
    /**
     * Valid Adapters
     * @var string
     */
    protected $validAdapters = array(
        'json',
        'html',
        'bin',
        'csv',
        'cli',
    );

    /**
     * Build
     * Construct the appropriate adapter for the document and use it's
     * build functionality to construct the doc's contents.   
     *
     * @param   DocumentInterface   $doc
     * @return  string
     */
    public function build(MessageInterface $doc)
    {   
		if (! $msg->isDoc()) {
			return false;
		}

		$doc     = $msg->get('doc');
        $adapter = $this->createAdapter($doc);
        return $adapter->build($doc);
    }

    /**
     * Render
     * Construct the appropriate adapter for the document and use it's
     * render functionality to output the document's contents.
     *
     * @param   DocumentInterface   $msg
     * @return  void
     */
    public function render(MessageInterface $doc)
    {
		if (! $msg->isDoc()) {
			return false;
		}

		$doc     = $msg->get('doc');
        $adapter = $this->createAdapter($doc);
        $adapter->render($doc);
    }

    /**
     * @return array
     */
    protected function getValidAdapters()
    {
        return $this->validAdapters;
    }

    /**
     * Create Adapter
     * Returns an adapter of the given document object.  Parses the
     * class name to find a matching adapter for output.
     *
     * @param   DocumentInterface   $doc
     * @return  AdapterAbstract
     */
    protected function createAdapter(DocumentInterface $doc)
    {
        $format  = $doc->getType();

        $validAdapters = $this->getValidAdapters();
        $format        = strtolower($format);
        if (! in_array($format, $validAdapters)) {
            throw new Exception('Invalid return format');
        }

        $className = __NAMESPACE__. ucfirst($format);
        return new $className();
    }
}
