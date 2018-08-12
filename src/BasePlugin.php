<?php
/**
 * @file
 * BasePlugin.php
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 * @copyright (c) 2014-2018 Ouest Systèmes Informatiques (OSInet)
 * @license Apache License 2.0 or later
 */

namespace OSInet\Beanstalkd\Munin;

use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;

abstract class BasePlugin implements PluginInterface
{
    /**
     * @var \Pheanstalk\PheanstalkInterface
     */
    public $server;


    /**
     * @return static
     */
    public static function createFromGlobals()
    {
        $host   = getenv('HOST') ?: 'localhost';
        $port   = (int) (getenv('PORT') ?: 11300);
        $server = new Pheanstalk($host, $port);
        return new static($server);
    }


    public function __construct(PheanstalkInterface $server)
    {
        $this->server = $server;
    }


    public function run(array $argv)
    {
        $ret = (count($argv) == 2 && $argv[1] === 'config')
          ? $this->config()
          : $this->data();

        return $ret;
    }
}
