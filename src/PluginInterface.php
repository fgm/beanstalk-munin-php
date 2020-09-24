<?php

/**
 * @file
 * Munin plugin interface
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 * @copyright 2014-2020 Ouest Systèmes Informatiques (OSInet)
 * @license Apache 2.0 or later
 */

declare(strict_types=1);

namespace OSInet\Beanstalkd\Munin;

interface PluginInterface
{

    /**
     * Implements Munin "config" command.
     *
     * @return string
     */
    public function config(): string;

    /**
     * Implements Munin "data" command.
     *
     * @return string
     */
    public function data(): string;
}
