<?php
/**
 * @file
 * Munin plugin interface
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 * @copyright 2014-2018 Ouest Systèmes Informatiques (OSInet)
 * @license Apache 2.0 or later
 */

namespace OSInet\Beanstalkd\Munin;

interface PluginInterface
{
    /**
     * Implements Munin "config" command.
     *
     * @return string
     */
    public function config();

    /**
     * Implements Munin "data" command.
     *
     * @return string
     */
    public function data();
}
