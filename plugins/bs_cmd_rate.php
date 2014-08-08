#!/usr/bin/env php
<?php
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../lib/lib.php";

use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;

class P {
  /**
   * @var \Pheanstalk\PheanstalkInterface
   */
  public $server;

  public $cmds = [
    ['put', 'cmd-put', 'Put'],
    ['reserve', 'cmd-reserve', 'Reserve'],
    ['reserve_timeout', 'cmd-reserve-with-timeout', 'Reserve with timeout'],
    ['delete', 'cmd-delete', 'Delete'],
    ['touch', 'cmd-touch', 'Touch'],
    ['release', 'cmd-release', 'Release'],
    ['bury', 'cmd-bury', 'Bury']
  ];

  public static function createFromGlobals() {
    $host = isset($_ENV['HOST']) ? $_ENV['HOST'] : 'localhost';
    $port = isset($_ENV['PORT']) ? $_ENV['PORT'] : 11300;
    $server = new Pheanstalk($host, $port);
    return new static($server);
  }

  public function __construct(PheanstalkInterface $server) {
    $this->server = $server;
  }

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

if (is_main($argc, $argv)) {
  $p = P::createFromGlobals();
  if ($argc == 2 && $argv[1] === 'config') {
    echo $p->config();
  }
  else{
    echo $p->data();
  }
}
