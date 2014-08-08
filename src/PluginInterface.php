<?php
/**
 * @file
 * Munin plugin interface
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2014 Ouest Systèmes Informatiques (OSInet).
 *
 * @license Apache 2.0
 */

namespace OSInet\Beanstalkd\Munin;


interface PluginInterface {
  /**
   * @return string
   */
  public function config();

  /**
   * @return string
   */
  public function data();

}
