#!/usr/bin/env php
<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2011 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
use Appfuel\AppManager;

$base = realpath(dirname(__FILE__));

openlog("appfuel", LOG_CONS|LOG_PID|LOG_NDELAY, LOG_LOCAL7);
syslog(LOG_ERR, array(1,2,3)); 
closelog();
