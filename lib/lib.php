<?php

/**
 * Is the caller the main file for the currently running application ?
 *
 * @param int $argc
 * @param array $argv
 *
 * @return bool
 */
function is_main($argc, array $argv = array()) {
  $stack = debug_backtrace();
  $caller = $stack[0]['file'];
  $main = realpath($argv[0]);
  $ret = $main === $caller;
  return $ret;
}
