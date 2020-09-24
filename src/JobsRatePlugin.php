<?php

/**
 * @file
 * Munin plugin for Beanstalkd "jobs rate" monitoring
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 * @copyright (c) 2014-2020 Ouest Systèmes Informatiques (OSInet)
 * @license Apache License 2.0 or later
 */

declare(strict_types=1);

namespace OSInet\Beanstalkd\Munin;

class JobsRatePlugin extends BasePlugin
{

    /**
     * {@inheritdoc}
     */
    public function config(): string
    {
        return <<<'CONFIG'
graph_title Job Rate
graph_vlabel Jobs per ${graph_period}
graph_category Beanstalk
graph_args --lower-limit 0
graph_scale no
queue_jobs.label Jobs
queue_jobs.type DERIVE
queue_jobs.min 0

CONFIG;
    }

    /**
     * {@inheritdoc}
     */
    public function data(): string
    {
        $stats = $this->server->stats();
        return sprintf("queue_jobs.value %d\n", $stats['total-jobs']);
    }
}
