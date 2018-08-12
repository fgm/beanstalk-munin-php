<?php

/**
 * @file
 * Munin plugin for Beanstalkd "age of waiting jobs in tubes" monitoring
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 * @copyright (c) 2014-2018 Ouest Systèmes Informatiques (OSInet)
 * @license Apache License 2.0 or later
 */

namespace OSInet\Beanstalkd\Munin;

use Pheanstalk\Exception\ServerException;

class QueueAgePlugin extends BasePlugin
{
    public $tubes = [];

    public static function cleanTube($tube)
    {
        return str_replace('.', '_', $tube);
    }

    public static function createFromGlobals()
    {
        /** @var \OSInet\Beanstalkd\Munin\QueueAgePlugin $instance */
        $instance = parent::createFromGlobals();

        $tubes = getenv('TUBES') ?: 'default';
        $tubesArray = explode(' ', $tubes);

        foreach ($tubesArray as $tube) {
            $instance->tubes[$instance->cleanTube($tube)] = $tube;
        }

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function config()
    {
        $ret = <<<'EOT'
graph_title Job Age
graph_vlabel Max Age
graph_category Beanstalk
graph_args --lower-limit 0
graph_scale no

EOT;

        foreach (array_keys($this->tubes) as $clean) {
            $ret .= sprintf("%s_jobs.label %s\n", $clean, $clean);
            $ret .= sprintf("%s_jobs.type GAUGE\n", $clean)
              . sprintf("%s_jobs.min 0\n", $clean);
        }
        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function data()
    {
        $server = $this->server;
        $ret = '';

        foreach ($this->tubes as $clean => $tube) {
            $server->useTube($tube);
            try {
                $job = $server->peekReady();
            } catch (ServerException $e) {
                $job = null;
            }

            $val = isset($job)
              ? $server->statsJob($job)['age']
              : 0;

            $ret .= sprintf("%s_jobs.value %d\n", $clean, $val);
        }

        return $ret;
    }
}
