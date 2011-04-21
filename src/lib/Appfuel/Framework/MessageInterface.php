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

use Appfuel\Stdlib\Data\DictionaryInterface;

/**
 * The Message is used as a uniform interface to deliver a payload of 
 * data across multiple framework layers. The message goes through
 * normalization and routing. 
 */
interface MessageInterface extends DictionaryInterface
{
    public function isRoute();
    public function isRequest();
    public function isDoc();
    public function isDocRender();
    public function enableDocRender();
    public function disableDocRender();
}
