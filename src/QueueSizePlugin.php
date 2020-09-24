<?php

/**
 * @file
 * Munin plugin for Beanstalkd queue size monitoring
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 * @copyright (c) 2014-2020 Ouest Systèmes Informatiques (OSInet)
 * @license Apache License 2.0 or later
 */

declare(strict_types=1);

namespace OSInet\Beanstalkd\Munin;

class QueueSizePlugin extends BasePlugin
{

    public array $jobTypes = [
      ['ready', 'current-jobs-ready', 'Ready'],
      ['urgent', 'current-jobs-urgent', 'Urgent'],
      ['reserved', 'current-jobs-reserved', 'Reserved'],
      ['delayed', 'current-jobs-delayed', 'Delayed'],
      ['buried', 'current-jobs-buried', 'Buried'],
    ];

    public static function cleanTube($tube)
    {
        return str_replace('.', '_', $tube);
    }

    /**
     * {@inheritdoc}
     */
    public function config(): string
    {
        $ret = <<<'CONFIG'
graph_title Queue Size
graph_vlabel Number of jobs in the queue
graph_category Beanstalk
graph_args --lower-limit 0
graph_scale no

CONFIG;

        foreach ($this->jobTypes as $jobType) {
            [$name, , $label] = $jobType;
            $ret .= sprintf("%s.label %s\n", $name, $label)
              . sprintf("%s.type GAUGE\n", $name)
              . sprintf("%s.min 0\n", $name);
        }
        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function data(): string
    {
        $server = $this->server;
        $stats = $server->stats();
        $ret = '';
        foreach ($this->jobTypes as $jobType) {
            [$name, $machine,] = $jobType;
            $ret .= sprintf("%s.value %d\n", $name, $stats[$machine]);
        }

        return $ret;
    }
}
