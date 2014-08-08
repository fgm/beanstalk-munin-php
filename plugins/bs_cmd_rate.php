#!/usr/bin/env php
<?php
/**
 * @file
 * Beanstalkd "command rate" Munin plugin.
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license Apache 2.0
 */

require_once __DIR__ . "/../vendor/autoload.php";

use OSInet\Beanstalkd\Munin\BasePlugin;

class P extends BasePlugin {
  public $cmds = [
    ['put', 'cmd-put', 'Put'],
    ['reserve', 'cmd-reserve', 'Reserve'],
    ['reserve_timeout', 'cmd-reserve-with-timeout', 'Reserve with timeout'],
    ['delete', 'cmd-delete', 'Delete'],
    ['touch', 'cmd-touch', 'Touch'],
    ['release', 'cmd-release', 'Release'],
    ['bury', 'cmd-bury', 'Bury']
  ];

  /**
   * Implement Munin "config" command.
   */
  public function config() {
    $ret = "graph_title Command Rate\n"
      . 'graph_vlabel Commands per ${graph_period}' . "\n"
      . "graph_category Beanstalk\n"
      . "graph_args --lower-limit 0\n"
      . "graph_scale no\n";

    foreach ($this->cmds as $cmd) {
      list($name,, $label) = $cmd;
      $ret .= sprintf("cmd_%s.label %s\n", $name, $label)
        . sprintf("cmd_%s.type DERIVE\n", $name)
        . sprintf("cmd_%s.min 0\n", $name);
    }
    return $ret;
  }

  /**
   * Implement Munin "data" command.
   */
  public function data() {
    $stats = $this->server->stats();
    $ret = '';
    foreach ($this->cmds as $cmd) {
      list($name, $counter,) = $cmd;
      $ret .= sprintf("cmd_%s.value %d\n", $name, $stats[$counter]);
    }
    return $ret;
  }
}

$p = P::createFromGlobals();
echo $p->run($argv);
