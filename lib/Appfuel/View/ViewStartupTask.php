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
namespace Appfuel\View;

use InvalidArgumentException,
	Appfuel\Kernel\Startup\StartupTaskAbstract;

/**
 * Allow configuration for the resource management.  This will configure
 * the base url that serves up resources as well as toggling wether
 * the resources should be combo'd together or loaded individually.
 */
class ViewStartupTask extends StartupTaskAbstract 
{
	/**
	 * Assign the registry keys to be pulled from the kernel registry
	 * 
	 * @return	OrmStartupTask
	 */
	public function __construct()
	{
		$this->setRegistryKeys(array('resource-manager', 'url'));
	}
	
    /**
	 * @param	array	$params		config params 
	 * @return	null
	 */
	public function execute(array $params = null)
	{
        if (! isset($params['resource-manager'])) {
            throw new InvalidArgumentException(
                'Missing key for resource start up -(resource-manager)'
            );
        }

        $resourceParams = $params['resource-manager'];
        if (! isset($resourceParams['url'])) {
            throw new InvalidArgumentException(
                'Missing key for resource start up -(url)'
            );
        }
        
        $isRelative = false;
        if (isset($resourceParams['is-relative'])) {
            $isRelative = $resourceParams['is-relative'];
        }
       
        $url = $resourceParams['url'];
        if (empty($url)) {
            throw new InvalidArgumentException(
                'Resource url cannot be an empty string'
            );
        }

        $scheme = 'http://';
        if (isset($_SERVER['HTTPS'])) {
            $isSsl  = $params['url']['protocol'] === 'https' &&
                      $_SERVER['HTTPS'] === 'on';
            if ($isSsl) {
                $scheme = 'https://';
            }
        }
        $url = $scheme . $url;


        $isBuild = true;
        if (isset($resourceParams['is-build'])) {
            $isBuild = $resourceParams['is-build'];
        }
        
        if (! defined('AF_RESOURCE_URL')) {
            define('AF_RESOURCE_URL', $url);
        }
        
        if (! defined('AF_IS_RESOURCE_BUILD')) {
            define('AF_IS_RESOURCE_BUILD', $isBuild);
        }
    }
}
