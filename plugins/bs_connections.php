#!/usr/bin/env php
<?php
/**
 * @file
 * Beanstalkd "connections" Munin plugin.
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
  /**
   * Implement Munin "config" command.
   */
  public function config() {
    $ret = <<<EOT
graph_title Open connections
graph_vlabel Connections
graph_category Beanstalk
graph_args --lower-limit 0
graph_scale no
connections.label Connections
connections.type GAUGE
connections.min 0

EOT;

    return $ret;
  }

  /**
   * Implement Munin "data" command.
   */
  public function data() {
    $stats = $this->server->stats();
    $ret = sprintf("connections.value %d\n", $stats['current-connections']);
    return $ret;
  }
}

$p = P::createFromGlobals();
echo $p->run($argv);
