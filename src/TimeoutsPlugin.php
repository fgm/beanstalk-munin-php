<?php

/**
 * @file
 * Munin plugin for Beanstalkd timeouts monitoring
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 * @copyright (c) 2014-2020 Ouest Systèmes Informatiques (OSInet)
 * @license Apache License 2.0 or later
 */

declare(strict_types=1);

namespace OSInet\Beanstalkd\Munin;

class TimeoutsPlugin extends BasePlugin
{

    /**
     * {@inheritdoc}
     */
    public function config(): string
    {
        return <<<'CONFIG'
graph_title Job Timeouts
graph_vlabel Timeouts per ${graph_period}
graph_category Beanstalk
graph_args --lower-limit 0
graph_scale no
timeouts.label Timeouts
timeouts.type DERIVE
timeouts.min 0

CONFIG;
    }

    /**
     * {@inheritdoc}
     */
    public function data(): string
    {
        $stats = $this->server->stats();
        return sprintf("timeouts.value %d\n", $stats['job-timeouts']);
    }
}
