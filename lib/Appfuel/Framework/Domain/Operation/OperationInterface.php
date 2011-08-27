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
namespace Appfuel\Framework\Domain\Operation;


/**
 */
interface OperationInterface
{
	public function getName();
	public function setName($text);
	public function getDescription();
	public function setDescription($text);
	public function getAccessPolicy();
	public function setAccessPolicy($policy);
	public function getRoute();
	public function setRoute($string);
	public function getDefaultFormat();
	public function setDefaultFormat($string);
	public function getOpClass();
	public function setOpClass($class);
	public function getRequestType();
	public function setRequestType($type);
	public function getControllerDetail();
	public function setControllerDetail($actionNs);
}
