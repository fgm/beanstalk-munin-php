<?php
/**
 * @file
 * BasePlugin.php
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace OSInet\Beanstalkd\Munin;


use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;

abstract class BasePlugin implements PluginInterface{
  /**
   * @var \Pheanstalk\PheanstalkInterface
   */
  public $server;

  /**
   * @return static
   */
  public static function createFromGlobals() {
    $host = isset($_ENV['HOST']) ? $_ENV['HOST'] : 'localhost';
    $port = isset($_ENV['PORT']) ? $_ENV['PORT'] : 11300;
    $server = new Pheanstalk($host, $port);
    return new static($server);
  }

  public function __construct(PheanstalkInterface $server) {
    $this->server = $server;
  }

  public function run(array $argv) {
    if (count($argv) == 2 && $argv[1] === 'config') {
      $ret = $this->config();
    }
    else{
      $ret = $this->data();
    }

    return $ret;
  }
}
