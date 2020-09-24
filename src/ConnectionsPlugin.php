<?php

/**
 * @file
 * Munin plugin for Beanstalkd connections monitoring
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 * @copyright (c) 2014-2020 Ouest Systèmes Informatiques (OSInet)
 * @license Apache License 2.0 or later
 */

declare(strict_types=1);

namespace OSInet\Beanstalkd\Munin;

class ConnectionsPlugin extends BasePlugin
{

    /**
     * {@inheritdoc}
     */
    public function config(): string
    {
        return <<<CONFIG
graph_title Open connections
graph_vlabel Connections
graph_category Beanstalk
graph_args --lower-limit 0
graph_scale no
connections.label Connections
connections.type GAUGE
connections.min 0

CONFIG;
    }

    /**
     * {@inheritdoc}
     */
    public function data(): string
    {
        $stats = $this->server->stats();
        return sprintf("connections.value %d\n", $stats['current-connections']);
    }
}
