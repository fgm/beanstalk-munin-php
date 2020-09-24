#!/usr/bin/env php
<?php

/**
 * @file
 * Munin plugin for Beanstalkd "jobs rate" monitoring
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 * @copyright (c) 2014-2020 Ouest Systèmes Informatiques (OSInet)
 * @license Apache License 2.0 or later
 */

namespace OSInet\Beanstalkd\Munin;

require_once __DIR__ . "/../vendor/autoload.php";

$p = JobsRatePlugin::createFromGlobals();
echo $p->run($argv);
