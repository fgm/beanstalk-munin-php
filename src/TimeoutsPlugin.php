<?php

/**
 * @file
 * Munin plugin for Beanstalkd timeouts monitoring
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 * @copyright (c) 2014-2018 Ouest Systèmes Informatiques (OSInet)
 * @license Apache License 2.0 or later
 */

namespace OSInet\Beanstalkd\Munin;

class TimeoutsPlugin extends BasePlugin
{
    /**
     * {@inheritdoc}
     */
    public function config()
    {
        $ret = <<<'EOT'
graph_title Job Timeouts
graph_vlabel Timeouts per ${graph_period}
graph_category Beanstalk
graph_args --lower-limit 0
graph_scale no
timeouts.label Timeouts
timeouts.type DERIVE
timeouts.min 0

EOT;

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function data()
    {
        $stats = $this->server->stats();
        $ret = sprintf("timeouts.value %d\n", $stats['job-timeouts']);
        return $ret;
    }
}
