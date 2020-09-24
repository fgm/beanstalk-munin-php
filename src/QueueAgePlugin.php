<?php

/**
 * @file
 * Munin plugin for Beanstalkd "age of waiting jobs in tubes" monitoring
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 * @copyright (c) 2014-2020 Ouest Systèmes Informatiques (OSInet)
 * @license Apache License 2.0 or later
 */

declare(strict_types=1);

namespace OSInet\Beanstalkd\Munin;

use Pheanstalk\Exception;
use Pheanstalk\Exception\CommandException;

class QueueAgePlugin extends BasePlugin
{

    public array $tubes = [];

    public static function createFromGlobals(): QueueAgePlugin
    {
        /** @var \OSInet\Beanstalkd\Munin\QueueAgePlugin $instance */
        $instance = parent::createFromGlobals();

        $tubes = getenv('BEANSTALKD_TUBES') ?: 'default';
        $tubesArray = explode(' ', $tubes);

        foreach ($tubesArray as $tube) {
            $instance->tubes[$instance->cleanTube($tube)] = $tube;
        }

        return $instance;
    }

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
graph_title Job Age
graph_vlabel Max Age
graph_category Beanstalk
graph_args --lower-limit 0
graph_scale no

CONFIG;

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
    public function data(): string
    {
        $server = $this->server;
        $ret = '';

        foreach ($this->tubes as $clean => $tube) {
            $server->useTube($tube);
            try {
                $job = $server->peekReady();
            } catch (\Exception $exc) {
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
