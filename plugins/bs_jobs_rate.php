#!/usr/bin/env php
<?php
/**
 * @file
 * Beanstalk "jobs rate" Munin plugin.
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license Apache 2.0
 */

namespace OSInet\Beanstalkd\Munin;

require_once __DIR__ . "/../vendor/autoload.php";

class JobsRatePlugin extends BasePlugin {
  /**
   * Implement Munin "config" command.
   */
  public function config() {
    $ret = <<<'EOT'
graph_title Job Rate
graph_vlabel Jobs per ${graph_period}
graph_category Beanstalk
graph_args --lower-limit 0
graph_scale no
queue_jobs.label Jobs
queue_jobs.type DERIVE
queue_jobs.min 0
EOT;

    return $ret;
  }

  /**
   * Implement Munin "data" command.
   */
  public function data() {
    $stats = $this->server->stats();
    $ret = sprintf("queue_jobs.value %d\n", $stats['total-jobs']);
    return $ret;
  }
}

$p = JobsRatePlugin::createFromGlobals();
echo $p->run($argv);
