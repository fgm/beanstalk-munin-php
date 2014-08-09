#!/usr/bin/env php
<?php
/**
 * @file
 * Beanstalk "age of waiting jobs in tubs" Munin plugin.
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license Apache 2.0
 */

namespace OSInet\Beanstalkd\Munin;

require_once __DIR__ . "/../vendor/autoload.php";

class QueueSizePlugin extends BasePlugin {

  public $job_types = [
    ['ready', 'current-jobs-ready', 'Ready'],
    ['urgent', 'current-jobs-urgent', 'Urgent'],
    ['reserved', 'current-jobs-reserved', 'Reserved'],
    ['delayed', 'current-jobs-delayed', 'Delayed'],
    ['buried', 'current-jobs-buried', 'Buried']
  ];

  public static function cleanTube($tube) {
    return str_replace('.', '_', $tube);
  }

  /**
   * Implement Munin "config" command.
   */
  public function config() {
    $ret = <<<'EOT'
graph_title Queue Size
graph_vlabel Number of jobs in the queue
graph_category Beanstalk
graph_args --lower-limit 0
graph_scale no

EOT;

    foreach ($this->job_types as $job_type) {
      list($name, , $label) = $job_type;
      $ret .= sprintf("%s.label %s\n", $name, $label)
        . sprintf("%s.type GAUGE\n", $name)
        . sprintf("%s.min 0\n", $name);
    }
    return $ret;
  }

  /**
   * Implement Munin "data" command.
   */
  public function data() {
//      print '%s.value %d' % (j[0], stats[j[1]])
//
    $server = $this->server;
    $stats = $server->stats();
    $ret = '';
    foreach ($this->job_types as $job_type) {
      list($name, $machine, ) = $job_type;
      $ret .= sprintf("%s.value %d\n", $name, $stats[$machine]);
    }

    return $ret;
  }
}

$p = QueueSizePlugin::createFromGlobals();
echo $p->run($argv);
