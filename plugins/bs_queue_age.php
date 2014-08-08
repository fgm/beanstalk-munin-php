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

require_once __DIR__ . "/../vendor/autoload.php";

use OSInet\Beanstalkd\Munin\BasePlugin;

class P extends BasePlugin {

  public $tubes = array();

  public static function cleanTube($tube) {
    return str_replace('.', '_', $tube);
  }

  public static function createFromGlobals() {
    /** @var \P $instance */
    $instance = parent::createFromGlobals();

    $tubes = isset($_ENV['TUBES']) ? $_ENV['TUBES'] : 'default';
    $tubes_array = explode(' ', $tubes);

    foreach ($tubes_array as $tube) {
      $instance->tubes[$instance->cleanTube($tube)] = $tube;
    }

    return $instance;
  }

  /**
   * Implement Munin "config" command.
   */
  public function config() {
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
   * Implement Munin "data" command.
   */
  public function data() {
    $server = $this->server;
    $stats = $server->stats();
    $ret = '';
    foreach ($this->tubes as $clean => $tube) {
      $server->useTube($tube);
      $job = $server->peekReady();
      $data = $server->statsJob($job);
      $val = empty($job) ? 0 : $data['age'];
      $ret .= sprintf("%s_jobs.value %d\n", $clean, $val);
    }

    return $ret;
  }
}

$p = P::createFromGlobals();
echo $p->run($argv);
