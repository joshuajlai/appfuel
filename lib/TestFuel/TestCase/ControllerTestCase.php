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
namespace TestFuel\TestCase;

/**
 */
class ControllerTestCase extends BaseTestCase 
{
    /**
     * @return  ControllerInteface
     */
    public function getMockActionController()
    {
        /* namespace to the known action controller */
        $interface = 'Appfuel\Framework\App\Action\ControllerInterface';
        $methods = array(
            'addSupportedDocs',
            'addSupportedDoc',
            'getSupportedDocs',
            'isSupportedDoc',
            'initialize',
            'execute'
        );

        return $this->getMockBuilder($interface)
                    ->setMethods($methods)
                    ->getMock();
    }

	/**
	 * @return	Appfuel\Framework\App\RequestInterface
	 */
	public function getMockRequest()
	{
		$interface = 'Appfuel\Framework\App\Request\RequestInterface';
		$methods   = array(
			'isPost',
			'isGet',
			'isCli',
			'getMethod',
			'getRouteString',
			'getUriString',
			'getParamString',
			'get',
			'getAll',
		);

		return $this->getMockBuilder($interface)
					->disableOriginalConstructor()
					->setMethods($methods)
					->getMock();

	}

	/**
	 * @return	Appfuel\Framework\Domain\Operaton\OperatonInterface
	 */
	public function getMockOperation()
	{
		$interface = 'Appfuel\Framework\Domain\Operation\OperationInterface';
		$methods   = array(
			'getName',
			'setName',
			'getDescription',
			'setDescription',
			'getAccessPolicy',
			'setAccessPolicy',
			'getRoute',
			'setRoute',
			'getDefaultFormat',
			'setDefaultFormat',
			'getOpClass',
			'setOpClass',
			'getRequestType',
			'setRequestType',
			'getControllerDetail',
			'setControllerDetail'
		);

		return $this->getMockBuilder($interface)
					->disableOriginalConstructor()
					->setMethods($methods)
					->getMock();
	}
}
