<?php

/**
 * @file
 * Munin plugin for Beanstalkd queue size monitoring
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 * @copyright (c) 2014-2018 Ouest Systèmes Informatiques (OSInet)
 * @license Apache License 2.0 or later
 */

namespace OSInet\Beanstalkd\Munin;

class QueueSizePlugin extends BasePlugin
{
    public $jobTypes = [
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
    public function config()
    {
        $ret = <<<'EOT'
graph_title Queue Size
graph_vlabel Number of jobs in the queue
graph_category Beanstalk
graph_args --lower-limit 0
graph_scale no

EOT;

        foreach ($this->jobTypes as $jobType) {
            list($name, , $label) = $jobType;
            $ret .= sprintf("%s.label %s\n", $name, $label)
              . sprintf("%s.type GAUGE\n", $name)
              . sprintf("%s.min 0\n", $name);
        }
        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function data()
    {
        //      print '%s.value %d' % (j[0], stats[j[1]])
        //
        $server = $this->server;
        $stats = $server->stats();
        $ret = '';
        foreach ($this->jobTypes as $jobType) {
            list($name, $machine,) = $jobType;
            $ret .= sprintf("%s.value %d\n", $name, $stats[$machine]);
        }

        return $ret;
    }
}
