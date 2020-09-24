<?php

/**
 * @file
 * BasePlugin.php
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 * @copyright (c) 2014-2020 Ouest Systèmes Informatiques (OSInet)
 * @license Apache License 2.0 or later
 */

declare(strict_types=1);

namespace OSInet\Beanstalkd\Munin;

use Pheanstalk\Connection;
use Pheanstalk\Contract\PheanstalkInterface;
use Pheanstalk\Pheanstalk;
use Pheanstalk\SocketFactory;

abstract class BasePlugin implements PluginInterface
{

    public PheanstalkInterface $server;

    public function __construct(PheanstalkInterface $server)
    {
        $this->server = $server;
    }

    /**
     * @return static
     */
    public static function createFromGlobals(): PluginInterface
    {
        $host = getenv('BEANSTALKD_HOST') ?: 'localhost';
        $port = (int)(getenv('BEANSTALKD_PORT') ?: 11300);
        $timeout = (int)(getenv('BEANSTALKD_TIMEOUT') ?: 10);
        $server = new Pheanstalk(
            new Connection(new SocketFactory($host, $port, $timeout))
        );
        return new static($server);
    }

    public function run(array $argv): string
    {
        $ret = (count($argv) == 2 && $argv[1] === 'config')
          ? $this->config()
          : $this->data();

        return $ret;
    }
}
