<?php

/**
 * @file
 * Munin plugin for Beanstalkd Command Rate monitoring
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 * @copyright (c) 2014-2018 Ouest Systèmes Informatiques (OSInet)
 * @license Apache License 2.0 or later
 */

namespace OSInet\Beanstalkd\Munin;

class CommandRatePlugin extends BasePlugin
{
    public $cmds = [
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
    public function config()
    {
        $ret = "graph_title Command Rate\n"
          . 'graph_vlabel Commands per ${graph_period}' . "\n"
          . "graph_category Beanstalk\n"
          . "graph_args --lower-limit 0\n"
          . "graph_scale no\n";

        foreach ($this->cmds as $cmd) {
            list($name, , $label) = $cmd;
            $ret .= sprintf("cmd_%s.label %s\n", $name, $label)
              . sprintf("cmd_%s.type DERIVE\n", $name)
              . sprintf("cmd_%s.min 0\n", $name);
        }
        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function data()
    {
        $stats = $this->server->stats();
        $ret = '';
        foreach ($this->cmds as $cmd) {
            list($name, $counter,) = $cmd;
            $ret .= sprintf("cmd_%s.value %d\n", $name, $stats[$counter]);
        }
        return $ret;
    }
}
