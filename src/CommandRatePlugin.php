<?php

/**
 * @file
 * Munin plugin for Beanstalkd Command Rate monitoring
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 * @copyright (c) 2014-2020 Ouest Systèmes Informatiques (OSInet)
 * @license Apache License 2.0 or later
 */

declare(strict_types=1);

namespace OSInet\Beanstalkd\Munin;

class CommandRatePlugin extends BasePlugin
{

    public array $commands = [
      ['put', 'cmd-put', 'Put'],
      ['reserve', 'cmd-reserve', 'Reserve'],
      ['reserve_timeout', 'cmd-reserve-with-timeout', 'Reserve with timeout'],
      ['delete', 'cmd-delete', 'Delete'],
      ['touch', 'cmd-touch', 'Touch'],
      ['release', 'cmd-release', 'Release'],
      ['bury', 'cmd-bury', 'Bury'],
    ];

    /**
     * {@inheritdoc}
     */
    public function config(): string
    {
        $ret = <<<'CONFIG'
graph_title Command Rate
graph_vlabel Commands per ${graph_period}
graph_category Beanstalk
graph_args --lower-limit 0
graph_scale no

CONFIG;

        foreach ($this->commands as $cmd) {
            [$name, , $label] = $cmd;
            $ret .= sprintf("cmd_%s.label %s\n", $name, $label)
              . sprintf("cmd_%s.type DERIVE\n", $name)
              . sprintf("cmd_%s.min 0\n", $name);
        }
        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function data(): string
    {
        $stats = $this->server->stats();
        $ret = '';
        foreach ($this->commands as $cmd) {
            [$name, $counter,] = $cmd;
            $ret .= sprintf("cmd_%s.value %d\n", $name, $stats[$counter]);
        }
        return $ret;
    }
}
