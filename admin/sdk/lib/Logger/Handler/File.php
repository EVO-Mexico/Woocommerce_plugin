<?php

namespace Payments\Logger\Handler;

/**
 * Append message to the specified log file.
 * 
 * Usage:
 *
 *   $log_file = 'log.txt';
 *   Logger::handler (Payments\Logger\Handler\File::init ($log_file));
 *     
 *   Logger::log ('Log me');
 */
class File{
  public static function init($file){
    return function ($info, $buffered = false) use ($file) {
      $f = fopen($file, 'a+');
      if (!$f) {
        throw new \LogicException('Could not open file for writing');
      }

      if (!flock($f, LOCK_EX)) {
        throw new \RuntimeException('Could not lock file');
      }

      fwrite($f, ($buffered) ? $info : vsprintf(\Payments\Logger\Logger::$format, $info));
      flock($f, LOCK_UN);
      fclose($f);
    };
  }
}
